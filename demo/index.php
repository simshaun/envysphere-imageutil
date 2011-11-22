<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<title>EnvySphere Image Library Demo</title>
	<meta name="author" content="Shaun Simmons">

	<link rel="stylesheet" href="reset.css">
	<link rel="stylesheet" href="demo.css">
</head>
<body>

<div id="container">

	<div id="main">

		<h1>EnvySphere Image Library Demo</h1>

		<table>
			<tr>
				<td>
					<div class="heading">Resize (max 300 x 300, no crop)</div>
					<iframe width="300" height="300" frameborder="0" src="demo_image.php?w=300&h=300"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 200 x 200, no crop)</div>
					<iframe width="200" height="200" frameborder="0" src="demo_image.php?w=200&h=200"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 100 x 100, no crop)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?w=100&h=100"></iframe>
				</td>
			</tr>

			<tr>
				<td>
					<div class="heading">Resize (max 300 x 300, <b>crop</b>)</div>
					<iframe width="300" height="300" frameborder="0" src="demo_image.php?w=300&h=300&c=1"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 200 x 200, <b>crop</b>)</div>
					<iframe width="200" height="200" frameborder="0" src="demo_image.php?w=200&h=200&c=1"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 100 x 100, <b>crop to center</b>)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?w=100&h=100&c=1"></iframe>

					<div class="heading">Resize (max 100 x 100, <b>crop to left</b>)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?w=100&h=100&c=1&cd=l"></iframe>

					<div class="heading">Resize (max 100 x 100, <b>crop to right</b>)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?w=100&h=100&c=1&cd=r"></iframe>
				</td>
			</tr>
		</table>

		<p>&nbsp;</p>

		<table>
			<tr>
				<td>
					<div class="heading">Resize (max 300 x 300, no crop)</div>
					<iframe width="300" height="300" frameborder="0" src="demo_image.php?img=tall.jpg&w=300&h=300"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 200 x 200, no crop)</div>
					<iframe width="200" height="200" frameborder="0" src="demo_image.php?img=tall.jpg&w=200&h=200"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 100 x 100, no crop)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?img=tall.jpg&w=100&h=100"></iframe>
				</td>
			</tr>

			<tr>
				<td>
					<div class="heading">Resize (max 300 x 300, <b>crop</b>)</div>
					<iframe width="300" height="300" frameborder="0" src="demo_image.php?img=tall.jpg&w=300&h=300&c=1"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 200 x 200, <b>crop</b>)</div>
					<iframe width="200" height="200" frameborder="0" src="demo_image.php?img=tall.jpg&w=200&h=200&c=1"></iframe>
				</td>

				<td width="30">&nbsp;</td>

				<td>
					<div class="heading">Resize (max 100 x 100, <b>crop to center</b>)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?img=tall.jpg&w=100&h=100&c=1"></iframe>

					<div class="heading">Resize (max 100 x 100, <b>crop to top</b>)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?img=tall.jpg&w=100&h=100&c=1&cd=t"></iframe>

					<div class="heading">Resize (max 100 x 100, <b>crop to bottom</b>)</div>
					<iframe width="100" height="100" frameborder="0" src="demo_image.php?img=tall.jpg&w=100&h=100&c=1&cd=b"></iframe>
				</td>
			</tr>
		</table>

	</div>
</div>

</body>
</html>
