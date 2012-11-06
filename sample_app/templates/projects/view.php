<section>
    <div class="page-header">
        <h1><?= $project['name']; ?></h1>
    </div>
</section>
<?php if (!empty($errors)) { ?>
<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">x</button>
    <b>Oh no!</b>&nbsp;
    <?php foreach ($errors as $err) { ?>
        <?= $err;?>&nbsp;
    <?php } ?>
    </ul>
</div>
<?php } else if (!empty($success)) { ?>
<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">x</button>
    <b>Success!</b>&nbsp;<?= $success;?>
</div>
<?php } ?>
<form method="post" action="/projects/<?= $project['id']; ?>">
    <fieldset>
        <label>Name:</label><input type="text" name="name" placeholder="Enter a name" required="required" value="<?= $t->h($project['name']); ?>">
        <label>Description:</label><textarea name="description"><?= $t->h($project['description']); ?></textarea><br>
        <label class="checkbox" for="project-is_active">
            <input id="project-is_active" type="checkbox" name="is_active" value="1" <?php $t->iff($project['is_active'], 'checked="checked"'); ?>>
            Is active?
        </label>
        <button class="btn btn-success" type="submit">Save changes</button>
    </fieldset>
</form>
