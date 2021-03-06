<!DOCTYPE html>

<html>

<head>
    <title>
        Ajax CRUD
    </title>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4>
                            Ajax CRUD
                        </h4>
                    </div>

                    <div class="col-md-12 text-right mb-5">
                        <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct">
                            Create New Product</a>
                    </div>

                    <div class="filter-alphabet">
                        <ul class="nav nav-tabs alpha_filter">
                            <?php $__currentLoopData = range('A', 'Z'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alphabet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="nav-item" value=<?php echo e($alphabet); ?>><a class="nav-link" href="#"><?php echo e($alphabet); ?></a></li> 
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item" value=""><a class="nav-link" href="#">All</a></li> 
                        </ul>
                    </div>

                    <div class="col-md-12">
                        <table class="table table-bordered data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Details</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>

                <div class="modal-body">
                    <form id="productForm" name="productForm" class="form-horizontal">
                        <input type="hidden" name="product_id" id="product_id" />

                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Name</label>

                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Details</label>

                            <div class="col-sm-12">
                                <textarea id="detail" name="detail" required="" placeholder="Enter Details" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript">
    $(function () {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: "<?php echo e(route('ajaxproducts.index')); ?>",
            columns: [
                { data: "name", name: "name" },
                { data: "detail", name: "detail" },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                },
            ],
        });

        $("#createNewProduct").click(function () {
            $("#saveBtn").val("create-product");
            $("#saveBtn").html("Create");
            $("#product_id").val("");
            $("#productForm").trigger("reset");
            $("#modelHeading").html("Create New Product");
            $("#ajaxModel").modal("show");
        });

        $("body").on("click", ".editProduct", function () {
            var product_id = $(this).data("id");

            $.get(
                "<?php echo e(route('ajaxproducts.index')); ?>" + "/" + product_id + "/edit",
                function (data) {
                    $("#modelHeading").html("Edit Product");
                    $("#saveBtn").val("edit-user");
                    $("#saveBtn").html("Update");
                    $("#ajaxModel").modal("show");
                    $("#product_id").val(data.id);
                    $("#name").val(data.name);
                    $("#detail").val(data.detail);
                }
            );
        });

        $("#saveBtn").click(function (e) {
            e.preventDefault();
            $.ajax({
                data: $("#productForm").serialize(),
                url: "<?php echo e(route('ajaxproducts.store')); ?>",
                type: "POST",
                dataType: "json",
                success: function (data) {
                    $("#productForm").trigger("reset");
                    $("#ajaxModel").modal("hide");
                    table.ajax.reload();
                    table.draw();
                },

                error: function (data) {
                    console.log("Error:", data);
                    $("#saveBtn").html("Create");
                },
            });
        });

        $("body").on("click", ".deleteProduct", function () {
            var product_id = $(this).data("id");
            var result = confirm("Are You sure want to delete !");
            if (result) {
                $.ajax({
                    type: "DELETE",
                    url: "<?php echo e(route('ajaxproducts.store')); ?>" + "/" + product_id,
                    success: function (data) {
                        table.ajax.reload();
                        table.draw();
                    },

                    error: function (data) {
                        console.log("Error:", data);
                    },
                });
            } else {
                return false;
            }
        });

        $(document).ready(function(){
            $('ul.alpha_filter li').click(function(e) 
            {
                var alpha_search =  $(this).attr("value");
                var table = $(".data-table").DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: "<?php echo e(route('ajaxproducts.index')); ?>",
                        type: "GET",
                        data: function(d) {
                            d.alpha_search = alpha_search
                        }
                    },
                    columns: [
                        { data: "name", name: "name" },
                        { data: "detail", name: "detail" },
                        {
                            data: "action",
                            name: "action",
                            orderable: false,
                            searchable: false,
                        },
                    ],
                });
            });
        });
    });
</script>

</html><?php /**PATH C:\xampp\htdocs\test\resources\views/productAjax.blade.php ENDPATH**/ ?>