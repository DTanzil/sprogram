<div class="row" style="margin-bottom:10px;">
    <div class="container">
        <a class="btn btn-success" href=<?='"' . base_url() . 'Manage_admins/create' . '"'?>>New Admin</a>
    </div>
</div>

<div id="table-ctrl" class="row">
<div class="col-lg-3">
<input class="form-control" id='search' type="text" placeholder="Find in table"/>
</div>
</div>
<img class="loading" src="assets/images/ajax-loader.gif"/>
<table class="paginated tablesorter" style="visibility: hidden;">
	<thead>
		<tr>
        	<th>Edit</th>
            <th>First Name</th>
            <th class="sort-by-first">Last Name</th>
            <th>NetID</th>
            <th>Roles</th>
            <th>Active?</th>
            <th>Delete</th>
        </tr>
	</thead>
    <tbody>
<?php foreach($admins as $admin): ?>
    <tr>
    	<td>
        	<a class="btn btn-sm btn-primary" href=<?= '"' . base_url("Manage_admins/edit/{$admin['UserID']}") . '"' ?> >Edit</a>
        </td>
        <td>
            <?= $admin['UserFName'] ?> 
        </td>
        <td>
            <?= $admin['UserLName'] ?> 
        </td>
        <td>
            <?= $admin['NetID'] ?> 
        </td>
        <td>
            <?= $admin['privileges'] ?> 
        </td>
        <td>
            <?= $admin['AdminIsActive'] == 1 ? 'Active' : 'Inactive' ?>
        </td>
        <td>
            <button class="btn btn-sm btn-danger delete" value=<?="{$admin['UserID']}"?>>Delete</button>
        </td>

    </tr>
<?php endforeach ?>

</tbody>
</table>