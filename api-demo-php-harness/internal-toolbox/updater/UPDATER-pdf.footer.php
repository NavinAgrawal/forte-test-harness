<!DOCTYPE html>
<!-- This is the PDF footer - it puts the "Created On" date on bottom-left and page numbers bottom-right -->
<html>
<head>
<script>
    <!-- creates the page numbers -->
	function subst() {
		var vars = {};
		var x = window.location.search.substring(1).split('&');
		for (var i in x) { var z = x[i].split('=', 2); vars[z[0]] = unescape(z[1]); }
		var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
		for (var i in x) {
			var y = document.getElementsByClassName(x[i]);
			for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];
		}
	}
</script>
</head>

<!-- writes the "Created On" datestamp, and the page numbers -->
<?php date_default_timezone_set('America/Chicago'); ?>
<body style="border:0px none; margin: 0" onload="subst()">
<table width="96%" style="border-bottom: 0px solid white">
	<tr>
		<td class="section" width="4%">
		</td>
		<td width="42%" style="text-align:left; font-family:Calibri; font-size:13pt;">
			<?php echo date("M j Y") . ', ' . date("g:i a T"); ?>
		</td>
		<td width="54%" style="text-align:right; font-family:Calibri; font-size:13pt;">Page 
			<span class="page"></span>&nbsp;of&nbsp;<span class="topage"></span>
		</td>
	</tr>
</table>
</body>
</html>