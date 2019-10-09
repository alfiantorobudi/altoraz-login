<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg-6">

            <!-- form error dengan prefix dan suffix -->
            <!-- form error adalah helper dari form validasi di autoload, untuk menampilkan error -->
            <!-- apabila gagal diinput maka muncul form_error -->
            <?= form_error('role', '<div class="alert alert-danger" role="alert">', '</div>') ?>

            <!-- apabila berhasil input role -->
            <?= $this->session->flashdata('message') ?>

            <a href="" class="btn btn-primary mb-3" data-toggle="modal" data-target="#newRoleModal"> Add New Role</a>
            <table class=" table table-hover">
                <thead>
                    <tr>
                        <th scope="col">No.</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($role as $r) : ?>
                        <tr>
                            <th scope="row"><?= $i; ?></th>
                            <td><?= $r['role'] ?></td>
                            <td>
                                <a href="<?= base_url('admin/roleaccess/') . $r['id']; ?>" class="badge badge-warning">Access</a>
                                <a href="<?= base_url('admin/edit_role/') . $r['id']; ?>" class="badge badge-primary" data-toggle="modal" data-target="#editRoleModal">Edit</a>
                                <a href="<?= base_url('admin/deleteRole/') . $r['id']; ?>" class="badge badge-danger" onclick="return confirm ('Yakin mau menghapus?');">Delete</a>

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

<div class="modal fade" id="newRoleModal" tabindex="-1" role="dialog" aria-labelledby="newRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newRoleModalLabel">Add New Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/role'); ?> " method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="role" name="role" placeholder="Role Name">
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

<!-- MODAL EDIT ROLE  -->

<div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/edit_role/') . $r['id']; ?> " method="post">
                <input type="hidden" name="id_role" value="<?= $r['id'] ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="edit_role" name="edit_role" value="<?= $r['role']; ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>