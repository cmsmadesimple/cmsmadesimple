<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>CMS Made Simple - Error console</title>
		<style>
			body {
				min-width: 900px;
				font-family: sans-serif;
				color: #232323;
				line-height: 1.3;
				font-size: 12px;
				background: #f6f6f6
			}
			a {
				color: #232323;
				text-decoration: none;
				font-weight: bold;
			}
			#wrapper{
				border-radius: 6px;
				width: 75%;
				margin: auto;
				padding: 15px
			}
			#wrapper h1 {
				margin: 0;
				text-shadow: 1px 1px 0px black, 2px 2px 0px #232323, 3px 3px 0px #232323, 4px 4px 0px #232323, 5px 5px 0px #232323;
				color: #585858;
				font-size: 52px;
				font-family: sans-serif
			}
			#wrapper h1 span.circle {
				background: #232323;
				color: #fff;
				margin: 10px 10px 10px 0;
				text-align: center;
				line-height: 140px;
				text-shadow: none;
				display: inline-block;
				width: 140px;
				height: 140px;
				border-radius: 75px;
			}
			#wrapper pre {
				background: #232323;
				border-left: 10px solid #aaa;
				color: #fff;
				font-family: "Courier New", Courier, monospace, sans-serif;
				padding: 15px;
				overflow: auto;
				word-wrap: break-word;
				border-radius: 6px
			}
			#wrapper span.important {
				color: #fa8004;
				font-weight: bold;
				text-transform: uppercase
			}
		</style>
		<script language="javascript">
		function toggle() {
			var el = document.getElementById("show");
			var trace = document.getElementById("open");
				if(el.style.display == "block") {
					el.style.display = "none";
					trace.innerHTML = "View Full Trace &darr;";
				} else {
					el.style.display = "block";
					trace.innerHTML = "Close Full Trace &uarr;";
				}
			}
			</script>
	</head>
	<body>
		<div id="wrapper">
			<h1><span class="circle">OMG!</span> You broke this page.</h1>
			<div class="error">
				<h2><span class="important">Error:</span> at line {$e_line} in file {$e_file}:</h2>
				<p class="message">
					<strong>Message:</strong>
				</p>
				<pre>{$e_message}</pre>
				<p class="message">
					<a id="open" href="javascript:toggle();">View Full Trace &darr;</a>
				</p>
				<pre id="show" style="display: none;">{$e_trace}</pre>
			</div>
		</div>
	</body>
</html>
