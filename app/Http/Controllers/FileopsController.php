<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileopsController extends fl_{}

class fileops{
	public static function create_file_if_missing(string $path, ?string &$error = null): bool{
		$error = null;

		if ($path === ''){
			$error = 'Empty path';
			return false;
		}

		if (strpos($path, "\0") !== false) {
			$error = 'Path contains null byte';
			return false;
		}

		$lastErr = null;

		set_error_handler(function($errno, $errstr) use (&$lastErr) {
			$lastErr = $errstr;
			return true; // prevent PHP internal handler from running
		});

		try {
			// Resolve parent dir
			$dir = dirname($path);
			if ($dir === '' || $dir === '.') {
				$dir = getcwd();
				if ($dir === false){
					$error = 'Cannot resolve current working directory';
					return false;
				}
			}

			clearstatcache(true, $path);

			// If file already exists — ensure it's a regular file and writable
			if (file_exists($path)) {
				if (is_dir($path)) { $error = 'Target exists and is a directory'; return false; }
				if (is_link($path)) { $error = 'Target is a symlink'; return false; }
				if (!is_writable($path)) {
					@chmod($path, 0644);
					clearstatcache(true, $path);
					if (!is_writable($path)){
						$error = 'File exists but is not writable';
						return false;
					}
				}
				return true;
			}

			// Create parent directories if missing
			if (!is_dir($dir)) {
				$oldUmask = umask(0);
				$mk = mkdir($dir, 0755, true);
				umask($oldUmask);
				if ($mk === false && !is_dir($dir)) {
					$err = $lastErr ?? "mkdir() failed";
					$error = "Failed to create directory '{$dir}': {$err}";
					return false;
				}
			}

			// Ensure parent dir is writable
			if (!is_writable($dir)) {
				@chmod($dir, 0755);
				clearstatcache(true, $dir);
				if (!is_writable($dir)) { $error = "Directory '{$dir}' is not writable"; return false; }
			}

			// Basic filename sanity
			$base = basename($path);
			if ($base === '' || strlen($base) > 255) {
				$error = 'Invalid filename';
				return false;
			}

			// Atomic creation: fopen mode 'x' fails if file exists (race-safe)
			$handle = @fopen($path, 'x');

			if ($handle === false) {
				// Maybe another process created it in the tiny window between checks
				if (file_exists($path) && is_file($path) && is_writable($path)) {
					return true;
				}
				$err = $lastErr ?? 'Unable to open file for exclusive creation';
				$error = "Failed to create file '{$path}': {$err}";
				return false;
			}

			// Close handle, set safe perms, final sanity checks
			fclose($handle);
			@chmod($path, 0666);
			clearstatcache(true, $path);

			if (!is_file($path) || !is_writable($path)) {
				$error = 'Created file is not a regular writable file';
				return false;
			}

			return true;
		} finally {
			restore_error_handler();
		}
	}

	// file reading
	public static function simple_read_file($path,$createit = false,&$error = null) {
		$err = "";
		if($createit){
			c_file($path);
		}

		if(is_file($path)){
			if(is_readable($path)){
				return file_get_contents($path);
			} else {
				$error = "the file cannot be read";
			}
		} else {
			$error = "the file doesnt exist";
		}

		return null;
	}
	public static function safe_read_file(string $filePath, bool $persistentWait = false,int $maxRetries = 5,&$error = null): string|bool {
		# this is a paranoid version that assumes the file is constantly being used by other processes
		$maxRetries = $persistentWait ? $maxRetries : 1;
		$retries = 0;

		// echo "reading file\n";

		# the file is closed to prevent memory leaks and temporarily locked to prevent any other process from screwing the reads
		while ($retries < $maxRetries) {
			$handle = fopen($filePath, 'rb');

			if ($handle === false) {
				if (!$persistentWait) return false;

				usleep(400000); // 0.4 seconds
				$retries++;
				continue;
			}

			if (flock($handle, LOCK_SH)) {
				$size = filesize($filePath);

				if($size > 0){
					$contents = fread($handle, $size);
				} else {
					$contents = fread($handle,1);
				}
				usleep(400000); // 0.4 seconds
				flock($handle, LOCK_UN);
				fclose($handle);
				return $contents;
			}

			fclose($handle);
			if (!$persistentWait) break;
			usleep(400000); // 0.4 seconds
			$retries++;
		}

		usleep(400000); // 0.4 seconds

		return false;
	}

	// file writing (binary -> multimedia, text -> text stuff)
	public static function safe_write_binary(string $filePath, string $data, bool $persistentWait = false,int $maxRetries = 5,&$error = null): bool{
		return self::safe_write($filePath, $data, 'wb', $persistentWait,$maxRetries,$error);
	}
	public static function safe_write_text(string $filePath, string $data, bool $persistentWait = false,int $maxRetries = 5,&$error = null): bool{
		return self::safe_write($filePath, $data, 'w', $persistentWait,$maxRetries,$error);
	}
	public static function safe_append_binary(string $filePath, string $data, bool $persistentWait = false,int $maxRetries = 5,&$error = null): bool{
		return self::safe_write($filePath, $data, 'ab', $persistentWait,$maxRetries,$error);
	}
	public static function safe_append_text(string $filePath, string $data, bool $persistentWait = false,int $maxRetries = 5,&$error = null): bool{
		return self::safe_write($filePath, $data, 'a', $persistentWait,$maxRetries,$error);
	}
	private static function safe_write(string $filePath, string $data, string $mode, bool $persistentWait = false,int $maxRetries = 5,&$error = null): bool{
		$maxRetries = $persistentWait ? $maxRetries : 1;
		$retries = 0;

		while ($retries < $maxRetries) {
			$handle = fopen($filePath, $mode);

			if ($handle === false) {
				if (!$persistentWait) return false;
				usleep(400000);
				$retries++;
				continue;
			}

			if (flock($handle, LOCK_EX)) {
				@chmod($path, 0666);
				$result = fwrite($handle, $data);
				fflush($handle);
				flock($handle, LOCK_UN);
				fclose($handle);
				return ($result !== false && $result === strlen($data));
			}

			fclose($handle);
			if (!$persistentWait) break;
			usleep(400000);
			$retries++;
		}

		return false;
	}
}

class simplifier extends fileops{
	public static function c_file(string $path, string &$error = null): bool {
		return self::create_file_if_missing($path,$error);
	}
	public static function saferead($path,$wait = false,$tries = 5,&$error = null){
		return self::safe_read_file($path, $wait,$tries,$error);
	}
	public static function safe_read($data,&$error = null){
		$path = $data['path'] ?? 'path';
		$wait = $data['wait'] ?? false;
		$tries = $data['tries'] ?? 5;

		return self::safe_read_file($path, $wait,$tries,$error);
	}
	public static function safewrite_txt($path,$data,$wait = false,$tries = 5,&$error = null){
		return self::safe_write_text($path,$data, $wait,$tries,$error);
	}
}

class fl_ extends simplifier{}
