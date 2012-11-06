<section>
    <div class="page-header">
        <h1>Projects</h1>
    </div>
    <div class="container">
    <table class="table table-striped">
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Description</th>
            <th>Status</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
    <?php foreach ($projects as $project) { ?>
        <tr>
            <td><?= $t->h($project['id']); ?></td>
            <td><?= $t->h($project['name']); ?></td>
            <td><small><?= $t->h($project['description']); ?></small></td>
            <td>
            <?php if ($project['is_active']) { ?>
                <span class="label label-success">Active</span>
            <?php } else { ?>
                <span class="label label-important">Inactive</span> 
            <?php } ?>
            </td>
            <td>
                <small>
                    <a href="/projects/<?= $project['id']; ?>"><i class="icon-pencil"></i>&nbsp;Edit</a>
                </small>
            </td>
            <td>
                <small>
                    <a href="/projects/delete/<?= $project['id']; ?>"><i class="icon-remove"></i>&nbsp;Delete</a>
                </small>
            </td>
        </tr>
    <?php } ?>
    </table>
    </div>
</section>
<section>
    <form method="post" action="/">
        <fieldset>
            <legend>Create a new project</legend>
            <input type="text" name="name" placeholder="Enter your project's name..."><br>
            <button type="submit" class="btn btn-primary">Create</button>
        </fieldset>
    </form>
</section>
