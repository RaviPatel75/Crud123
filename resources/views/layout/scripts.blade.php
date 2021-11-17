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
            ajax: "{{ route('ajaxproducts.index') }}",
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
                "{{ route('ajaxproducts.index') }}" + "/" + product_id + "/edit",
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
                url: "{{ route('ajaxproducts.store') }}",
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
                    url: "{{ route('ajaxproducts.store') }}" + "/" + product_id,
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
                        url: "{{ route('ajaxproducts.index') }}",
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