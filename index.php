<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Memo App</title>

	<link rel="stylesheet" type="text/css" href="_assets/css/fonts.css">
	<link rel="stylesheet" type="text/css" href="_assets/css/w3.css">
	<link rel="stylesheet" type="text/css" href="_assets/css/fa-all.css">

	<script src="_assets/js/customalerter.js"></script>
	<script src="_assets/js/SuperScript.js"></script>
	<script src="_assets/js/toappend.js"></script>

	<style>
		/* ---------- VARIABLES ---------- */
		:root {
		    --bg-gradient-start: #272727;
		    --bg-gradient-end: #141111;
		    --card-bg: rgb(63 63 63 / 90%);
		    --dark-card-bg: #222;
		    --modal-bg: #1e1e1e;
		    --text-color: #eee;
		    --text-muted: #aaa;
		    --accent: #ff6600;
		    --accent-dark: #cc2e00;
		    --border-color: #333;
		    --shadow-light: rgba(0, 0, 0, 0.6);
		    --shadow-strong: rgba(0, 0, 0, 0.8);
		}

		/* ---------- RESET ---------- */
		* {
		    margin: 0;
		    padding: 0;
		    box-sizing: border-box;
		    font-family: "Inter_24pt", system-ui, sans-serif;
		    transition: 0.3s ease-out;
		}

		body {
		    background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
		    min-height: 100vh;
		    padding: 2rem;
		    color: var(--text-color);
		}

		/* ---------- HEADER ---------- */
		header {
		    display: flex;
		    justify-content: space-between;
		    align-items: center;
		    padding: 1.2rem 2rem;
		    background: var(--card-bg);
		    border-radius: 12px;
		    backdrop-filter: blur(8px);
		    box-shadow: 0 8px 32px var(--shadow-light);
		    margin-bottom: 2rem;
		}

		header h1 {
		    font-size: 1.8rem;
		    font-weight: 600;
		    color: var(--accent);
		}

		.btn-primary {
		    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
		    color: #fff;
		    border: none;
		    padding: 0.6rem 1.2rem;
		    font-size: 0.9rem;
		    border-radius: 50px;
		    cursor: pointer;
		}

		.btn-primary:hover {
		    transform: translateY(-2px);
		    box-shadow: 0 4px 14px rgba(255, 102, 0, 0.45);
		}

		/* ---------- MEMO GRID ---------- */
		#memo-container {
		    display: grid;
		    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
		    gap: 1.5rem;
		}

		.memo {
		    background: var(--card-bg);
		    border-radius: 12px;
		    padding: 1.5rem;
		    box-shadow: 0 4px 20px var(--shadow-light);
		    backdrop-filter: blur(6px);
		    cursor: pointer;
		}

		.memo:hover {
		    background: var(--dark-card-bg);
		    transform: translateY(-4px);
		    box-shadow: 0 8px 26px var(--shadow-strong);
		}

		.memo h3 {
		    font-size: 1.2rem;
		    margin-bottom: 0.5rem;
		    color: var(--accent);
		}

		.memo p {
		    margin-bottom: 0.75rem;
		    line-height: 1.45;
		    color: var(--text-color);
		}

		.memo small {
		    color: var(--text-muted);
		    font-size: 0.75rem;
		}

		/* ---------- MODAL ---------- */
		.modal {
		    position: fixed;
		    inset: 0;
		    background: rgba(0, 0, 0, 0.6);
		    backdrop-filter: blur(6px);
		    display: flex;
		    align-items: center;
		    justify-content: center;
		    z-index: 5;
		}

		.modal-content {
		    background: var(--modal-bg);
		    border-radius: 14px;
		    width: 90%;
		    max-width: 420px;
		    padding: 2rem;
		    box-shadow: 0 10px 40px var(--shadow-light);
		    animation: pop 0.25s ease-out;
		}

		@keyframes pop {
		    0% {
		        transform: scale(0.95);
		        opacity: 0;
		    }
		    100% {
		        transform: scale(1);
		        opacity: 1;
		    }
		}

		.modal-content h2 {
		    margin-bottom: 1.2rem;
		    font-size: 1.4rem;
		    color: var(--accent);
		    text-align: center;
		}

		#memoForm {
		    display: flex;
		    flex-direction: column;
		    gap: 1rem;
		}

		#memoForm input,
		#memoForm textarea {
		    padding: 0.8rem;
		    border: 1px solid var(--border-color);
		    border-radius: 8px;
		    font-size: 0.95rem;
		    background: #111;
		    color: var(--text-color);
		}

		#memoForm input:focus,
		#memoForm textarea:focus {
		    outline: none;
		    border-color: var(--accent);
		}

		/* ---------- VIEW MEMO MODAL ---------- */
		#viewMemoModal .modal-content {
		    max-width: 900px;
		    padding: 2rem;
		    max-height: 90vh;
		    overflow: auto;
		}

		.memo-detail h2 {
		    color: var(--accent);
		    margin-bottom: .75rem;
		}

		.memo-detail p {
		    line-height: 1.6;
		    margin-bottom: 1rem;
		    white-space: pre-wrap;
		    color: var(--text-color);
		}

		.memo-meta {
		    font-size: .8rem;
		    color: var(--text-muted);
		    border-top: 1px solid var(--border-color);
		    padding-top: .75rem;
		    margin-top: 1rem;
		}

		#closeViewBtn {
		    margin-top: 1.5rem;
		    width: 100%;
		}
	</style>
</head>
<body>
	<header>
		<h1>Memo App</h1>
		<button id="addMemoBtn" class="btn-primary">+ Add Memo</button>
	</header>

	<div id="memo-container">
		<p id="demo-text">No memos found. Start by adding one!</p>
	</div>

	<!-- Modal -->
	<div id="memoModal" class="modal" style="display: none;">
		<div class="modal-content">
			<h2>Add Memo</h2>
			<form id="memoForm">
				<input type="text" name="title" placeholder="Memo Title" required>
				<textarea name="message" placeholder="Memo Message" rows="7" required></textarea>
				<input type="password" name="key" placeholder="Access Key" required>
				<button type="submit" class="btn-primary">Save Memo</button>
			</form>
		</div>
	</div>

	<!-- View Memo Modal -->
	<div id="viewMemoModal" class="modal" style="display:none;">
		<div class="modal-content memo-detail">
			<button id="copybtn" class="btn-primary w3-right" data-copyme="#viewMessage"><i class="fa fa-copy"></i> copy</button>
			<h2 id="viewTitle"></h2>
			<p  id="viewMessage"></p>
			<div class="memo-meta">
				<span id="viewDates"></span>
			</div>
			<button id="closeViewBtn" class="btn-primary">Close</button>
		</div>
	</div>

	<script>
		const memoContainer = document.getElementById("memo-container");
		const demoText = document.getElementById("demo-text");
		const modal = document.getElementById("memoModal");
		const vmodal = document.querySelector('#viewMemoModal');
		const addBtn = document.getElementById("addMemoBtn");
		const form = document.getElementById("memoForm");
		let closeViewBtn = vmodal.querySelector('#closeViewBtn');
		let copybtn = vmodal.querySelector('#copybtn');

		// Fetch memos
		async function loadMemos() {
			try {
				const res = await fetch("_api/getmemos");
				const memos = await res.json();

				memoContainer.innerHTML = "";
				if (!memos.length) {
					memoContainer.appendChild(demoText);
					demoText.style.display = "block";
					return;
				}

				memos.forEach(memo => {
					const div = document.createElement("div");
					div.className = "memo";
					div.innerHTML = `
						<h3>${memo.name}</h3>
						<p>${memo.data.substr(0,24)}...</p>
						<small>Created: ${memo.datecreated} | Expiry: ${memo.expiry}</small>
					`;

					div.addEventListener('click',(e) => {
						showfullmemo(memo);
					});
					memoContainer.appendChild(div);
				});
			} catch (err) {
				console.error("Error loading memos:", err);
				demoText.textContent = "Failed to load memos.";
				demoText.style.display = "block";
			}
		}

		// Show modal
		addBtn.onclick = () => {
			modal.style.display = "flex";
		};
		closeViewBtn.onclick = () => {
			vmodal.style.display = "none";
		}
		copybtn.addEventListener('click',() => {
			let tocopy = document.querySelector(copybtn.dataset.copyme).innerText;
			copytext1(tocopy);
			alert_success("text copied successfully");

			vmodal.style.display = "none";
		})
		// Close modal when clicking outside
		window.onclick = (e) => {
			if (e.target === modal) {
				modal.style.display = "none";
			}

			if (e.target === vmodal) {
				vmodal.style.display = "none";
			}
		};

		// Handle form submit
		form.onsubmit = async (e) => {
			e.preventDefault();
			const formData = new FormData(form);
			try {
				const res = await fetch("_api/addmemo", {
					method: "POST",
					body: formData
				});
				const result = await res.json();

				alert_dark(result.message); // ✅ Alert success or error message from PHP

				if (result.success) {
					modal.style.display = "none";
					form.reset();
					loadMemos();
				}
			} catch (err) {
				alert_dark("Error saving memo. Please try again.");
			}
		};

		function showfullmemo(what) {
			console.log(what);
			let viewTitle = vmodal.querySelector('#viewTitle');
			let viewMessage = vmodal.querySelector('#viewMessage');
			let viewDates = vmodal.querySelector('#viewDates');
			let copybtn = vmodal.querySelector('#copybtn');

			viewTitle.innerText = what.name;
			viewMessage.innerText = what.data;
			viewDates.innerHTML = `Created: ${what.datecreated} | Expiry: ${what.expiry}`;

			vmodal.style.display = "flex";
		}

		// Initial load
		loadMemos();
	</script>
</body>
</html>
