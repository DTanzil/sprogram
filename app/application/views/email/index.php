<?php $sortMethod = '[[0, 0]]'; ?>

<div class="row" style="margin-bottom:10px;">
    <div class="container">
        <a class="btn btn-success" href=<?='"' . base_url() . 'Email/create' . '"'?>>New Email Template</a>
    </div>
</div>

<div id="table-ctrl" class="row">
<div class="col-lg-3">
<input class="form-control" id='search' type="text" placeholder="Find in table"/>
</div>
</div>
<img class="loading" src="assets/images/ajax-loader.gif"/>
<table class="paginated tablesorter" style="visibility: hidden;" >
	<thead>
		<tr>
            <th></th>
        	<th>Edit</th>
            <th>Template Name</th>
            <th>Recipients</th>
            <th>CC</th>
            <th>Action Name</th>

        </tr>
	</thead>
    <tbody>
<?php foreach($templates as $template): ?>
    <tr>
        <td><?= $template['EmailTemplateID'] ?></td>
        <td>
            <a class="btn btn-sm btn-primary" href=<?= '"' . base_url("Email/edit/{$template['EmailTemplateID']}") . '"' ?> >Edit</a>
        </td>
        <td><?= $template['EmailTemplateName'] ?></td>
        <td><?= $template['Recipients'] ?></td>
        <td><?= $template['CC'] ?></td>
        <td><?= $template['ActionName'] ?></td>
    </tr>
<?php endforeach ?>

</tbody>
</table>