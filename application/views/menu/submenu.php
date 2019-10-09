<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg">

            <!-- untuk form submenu make form_validation -->

            <?php if (validation_errors()) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= validation_errors(); ?>
                </div>
            <?php endif; ?>

            <!-- apabila berhasil input menu -->
            <?= $this->session->flashdata('message') ?>

            <a href="" class="btn btn-primary mb-3" data-toggle="modal" data-target="#newSubmenuModal"> Add New Submenu</a>
            <table class=" table table-hover">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Title</th>
                        <th scope="col">Menu</th>
                        <th scope="col">Url</th>
                        <th scope="col">Icon</th>
                        <th scope="col">Active</th>
                        <th scope="col">Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($submenu as $sm) : ?>
                        <tr>
                            <th scope="row"><?= $i; ?></th>
                            <td><?= $sm['title'] ?></td>
                            <td><?= $sm['menu'] ?></td>
                            <td><?= $sm['url'] ?></td>
                            <td><?= $sm['icon'] ?></td>
                            <td><?= $sm['is_active'] ?></td>
                            <td>
                                <a href="<?= base_url('menu/edit_submenu/') . $sm['id']; ?>" class="badge badge-primary" data-toggle="modal" data-target="#editSubmenuModal">Edit</a>
                                <a href="<?= base_url('menu/delete_submenu/') . $sm['id']; ?>" class="badge badge-danger" onclick="return confirm('Are You Sure?');">Delete</a>

                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>



</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->



<!-- MODAL  -->

<div class="modal fade" id="newSubmenuModal" tabindex="-1" role="dialog" aria-labelledby="newSubmenuModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newSubmenuModalLabel">Add New Submenu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('menu/submenu'); ?> " method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Submenu Title">
                    </div>
                    <div class="form-group">
                        <select name="menu_id" id="menu_id" class="form-control">

                            <option value="">Select Menu</option>
                            <?php foreach ($menu as $m) : ?>
                                <option value="<?= $m['id']; ?>"> <?= $m['menu']; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="url" name="url" placeholder="Submenu Url">
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="icon" name="icon" placeholder="Submenu Icon">
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Active?
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT SUBMENU -->
<div class="modal fade" id="editSubmenuModal" tabindex="-1" role="dialog" aria-labelledby="editSubmenuModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubmenuModalLabel">Edit Submenu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('menu/edit_submenu/'); ?> <?= $sm['id']; ?>" method="post">
                <input type="hidden" name="id" value="<?= $sm['id']; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="edit_title" name="edit_title" value="<?= $sm['title']; ?>">
                    </div>
                    <div class="form-group">
                        <select name="edit_menu_id" id="edit_menu_id" class="form-control">

                            <option value="">Select Menu</option>
                            <?php foreach ($menu as $m) : ?>
                                <option value="<?= $m['id']; ?>"> <?= $m['menu']; ?> </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="edit_url" name="edit_url" value="<?= $sm['url']; ?>">
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" id="edit_icon" name="edit_icon" value="<?= $sm['icon']; ?>">
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Active?
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>