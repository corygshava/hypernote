<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;

class PostController extends Controller {
	public function get_my_notes(Request $req){
		$res = $this->dftres;
		$alldata = $req->all();
		$msg = "didnt even get to start";
		$out = null;

		/*
		$passed = $req->validate([
			'condition' => ['required']
		]);
		// */

		// utils
			$update_runlog = function($wot) use (&$res){
				$res['runlog'][] = $wot;
			};
			$update_msg = function($wot,$logit = true) use (&$msg,&$update_runlog){
				$msg = $wot;
				if($logit){
					$update_runlog($wot);
				}
			};
			$_msg = function($w,$l = true) use (&$update_msg){
				$update_msg($w,$l);
			};
			$_rlg = function($w) use (&$update_runlog){
				$update_runlog($w);
			};

		// ops begin
		if($this->ili()){
			$res['success'] = true;
			$msg = "retreiving your notes";
            $user = $this->cur_user();

			$perPage = $req->input('per_page', 10);
			$search = $req->input('search');
			$sortBy = $req->input('sort_by', 'created_at');
			$sortDirection = $req->input('sort_direction', 'desc');

			// filters
			$status = $req->input('status',null);
			$with_results = $req->input('with_results',null);
			$sample_typ = $req->input('sample_type',null);
			$pay_status = $req->input('payment_status',null);
			$t_range = $req->input('timerange',null);

			$sortBy = $sortBy == null ? 'created_at' : $sortBy;

			$_rlg([
				"perPage" => $perPage,
				"search" => $search,
				"sortBy" => $sortBy,
				"sortDirection" => $sortDirection,
				"status" => $status,
				"with_results" => $with_results,
			]);

			// clean up sortBy to prevent issues
                /*
                if($sortBy == 'owner'){
                    $sortBy = 'user_id';
                } elseif($sortBy == 'task'){
                    $sortBy = 'task_id';
                }
                // */

			// return response()->json($res);

			$records = Posts::query()
                ->where('id','<>',null)
			;

			if($search){
				$records = $records
					->orWhere('title', 'like', "%{$search}%")
					->orWhere('body', 'like', "%{$search}%")
				;
				$_rlg("added search filter");
			}

			// filter for task status
			if($status !== null){
				$_rlg("added status filter -> $status");
				$records = $records->where('privacy_state',$status);
			}

			if($t_range){
				$records = $records->timeRange($t_range);
			}

			$sortBy = $sortBy == null || $sortBy == '' ? 'created_at' : $sortBy;

			$records = $records
				->orderBy($sortBy, $sortDirection)
				->paginate($perPage);

			/*
			for($i = 0;$i < count($users);$i++){
				$users[$i]->role_name = $users[$i]->myRoles?->role_name;
				$users[$i]->role_permissions = $users[$i]->myRoles?->role_permissions;
			}
			*/

			$res['result'] = true;
			$res['sendme'] = $records;
			$res['message'] = $msg;
		}

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
	}

	public function get_my_notes_stats(Request $req){
		$res = $this->dftres;
		$msg = "didnt even get to start";
		$alldata = $req->all();
		$out = null;

		/*
		$passed = $req->validate([
			'condition' => ['required']
		]);
		// */

		// utils
			$update_runlog = function($wot) use (&$res){
				$res['runlog'][] = $wot;
			};
			$update_msg = function($wot,$logit = true) use (&$msg,&$update_runlog){
				$msg = $wot;
				if($logit){
					$update_runlog($wot);
				}
			};
			$_msg = function($w,$l = true) use (&$update_msg){
				$update_msg($w,$l);
			};
			$_rlg = function($w) use (&$update_runlog){
				$update_runlog($w);
			};

			$stats = [];
			$_statme = function(array $s ) use (&$stats){
				$dvl = rand(0,500);
				$dft = [
					"value" => $dvl,
					"valtype" => 'number',			// either number, string, date or plain
					"label" => "Total Debtors",
					"type" => $dvl > 250 ? "success" : ($dvl > 200 ? "primary" : "danger"),
					"icon" => "defined:debtors"
				];
				$stats[] = [...$dft,...$s];
			};

		// ops begin
		if($this->ili()){
			$res['success'] = true;

			$_msg("trying to get expenses");

			$perPage = $req->input('per_page', 10);
			$search = $req->input('search',null);
			$status = $req->input('status',null);
			$sortBy = $req->input('sort_by', 'credit_balance');
			$sortDirection = $req->input('sort_direction', 'desc');
			$t_range = $req->input('timerange', null);

			// filters
			$ex_type = $req->input('expense_type',null);

			$search = $search == '*' ? null : $search;

			$res['runlog'][] = [
				"perPage" => $perPage,
				"search" => $search,
				"sortBy" => $sortBy,
				"sortDirection" => $sortDirection,
			];

			$records = Posts::query()
                ->where('id','<>',null)
			;

			if($search){
				$records = $records
					->orWhere('title', 'like', "%{$search}%")
					->orWhere('body', 'like', "%{$search}%")
				;
				$_rlg("added search filter");
			}

			// filter for task status
			if($status !== null){
				$_rlg("added status filter -> $status");
				$records = $records->where('privacy_state',$status);
			}

			if($t_range){
				$records = $records->timeRange($t_range);
			}

			$sortBy = $sortBy == null || $sortBy == '' ? 'created_at' : $sortBy;

			$posts = (clone $records);

			// the prev was left in case i feel like attaching the filters to stats too
			// stat of stats processing, get it :)
			$all_posts = Posts::all();

			// total expenses
			$statval = (clone $all_posts)->count();
			$_statme(
				[
					"value" => $statval,
					"label" => "All Notes",
					"type" => "primary",
					"icon" => "defined:notes"
				]
			);

			$statval = (clone $all_posts)->where('privacy_state',1)->count();
			$_statme(
				[
					"value" => $statval,
					"label" => "Public Notes",
					"type" => $statval > 0 ? "success" : "danger",
					"icon" => "defined:notes"
				]
			);
			$statval = (clone $all_posts)->where('privacy_state',2)->count();
			$_statme(
				[
					"value" => $statval,
					"label" => "Private Notes",
					"type" => $statval > 0 ? "success" : "danger",
					"icon" => "defined:notes"
				]
			);
			$statval = (clone $all_posts)->where('privacy_state',3)->count();
			$_statme(
				[
					"value" => $statval,
					"label" => "Unlisted Notes",
					"type" => $statval > 0 ? "success" : "danger",
					"icon" => "defined:notes"
				]
			);

			$out = $stats;

			/*
			for($i = 0;$i < count($users);$i++){
				$users[$i]->role_name = $users[$i]->myRoles?->role_name;
				$users[$i]->role_permissions = $users[$i]->myRoles?->role_permissions;
			}
			*/

			$_msg("all notes stats fetched");
			$res['result'] = true;
			$res['sendme'] = $out;
			$res['message'] = $msg;
		}

		// dd($res);

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
	}

    public function add_edit_note(Request $req){
		$res = $this->dftres;
		$alldata = $req->all();
		$msg = "didnt even get to start";
		$out = null;

		// /*
		$passed = $req->validate([
			'cur_purpose' => ['required'],
			'post_id' => ['sometimes'],
			'title' => ['required'],
			'post_body' => ['required'],
			'privacy_state' => ['required'],
			'tags_actual' => ['sometimes'],
			'doctype' => ['required','string'],
		]);
		// */

		// utils
			$update_runlog = function($wot) use (&$res){
				$res['runlog'][] = $wot;
			};
			$update_msg = function($wot,$logit = true) use (&$msg,&$update_runlog){
				$msg = $wot;
				if($logit){
					$update_runlog($wot);
				}
			};
			$_msg = function($w,$l = true) use (&$update_msg){
				$update_msg($w,$l);
			};
			$_rlg = function($w) use (&$update_runlog){
				$update_runlog($w);
			};

		// ops begin
		if($this->ili()){
			$res['success'] = true;
			$update_msg('starting the procedure');
			$reslt = false;
            $note = null;

            $_rlg(['passed' => $passed]);
            $meth = $passed['cur_purpose'];
            $user = self::cur_user();
            $u_id = $user->id;

            $tags = explode(',',$passed['tags_actual']);

            $newrec = [
                'privacy_state' => $passed['privacy_state'],
                'title' => $passed['title'],
                'body' => $passed['post_body'],
                'tags' => $tags,
                'doctype' => $passed['doctype'],
            ];

            if($meth == 'edit'){
                $_msg('editing the note');

                if($passed['post_id'] == null){
                    $_msg('no post_id passed');
                } else {
                    $p_id = $passed['post_id'];
                    $note = Posts::query()->where('id',$p_id)->first();

                    if($note == null){
                        $_msg("Invalid note id passed");
                    } else {
                        $_msg("Note found");

                        if($note->user_id !== $u_id){
                            $_msg('This note doesnt belong to you');
                        } else {
                            $_msg('editing note');
                            $note->update($newrec);
                            $reslt = true;
                        }
                    }
                }
            } else {
                $newrec['user_id'] = $u_id;
                $_msg('adding the note');

                $note = Posts::query()->create($newrec);
                $reslt = true;
                $_msg('Note added successfully');
            }

			$out = $note;
			// $reslt = false;

			$res['result'] = $reslt;
			$res['sendme'] = $out;
			$res['message'] = $msg;
		}

		if(!self::$showlogs){
			if(array_key_exists('runlog',$res)){
				unset($res['runlog']);
			}
		}

		return response()->json($res);
	}
}
