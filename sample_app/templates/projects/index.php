<h1>Projects</h1>
<ul>
<?php foreach ($projects as $project) { ?>
<li><?= $project['id'] ?> - <?= $project['name'] ?></li>
<?php } ?>
</ul>
<hr>
<h2>Add a new project</h2>
<form method="post" action="/">
    <input type="text" name="name" placeholder="Enter your project's name..."><input type="submit" value="Create">
</form>
