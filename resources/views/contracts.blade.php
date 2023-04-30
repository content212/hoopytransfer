@extends('test')

@section('title', 'Contracts')

@section('role', $role)

@section('name', $name)

@section('css')
<link href="{{ asset('js/ui/trumbowyg.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .popover {
            z-index: 999999;
        }

    </style>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="contracts_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Display Order</th>
                    <th>Position</th>
                    <th>Active</th>
                    <th>Edit</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="modal top fade" id="edit_modal" tabindex="-1" aria-labelledby="edit_modal_label" aria-hidden="true"
        data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="contracts_result"></span>
                    <form id="contract_form" method="GET">
                        <input type="hidden" name="id" id="id" value="-1">
                        <div class="row ">
                            <div class="col-md-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                            <div class="col-md-3">
                                <label for="prefix">Prefix</label>
                                <input type="text" class="form-control" name="prefix" id="prefix">
                            </div>
                            <div class="col-md-3">
                                <label for="suffix">Suffix</label>
                                <input type="text" class="form-control" name="suffix" id="suffix">
                            </div>
                            <div class="col-md-3">
                                <label for="display_order">Display Order</label>
                                <input type="number" class="form-control" name="display_order" id="display_order">
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">
                            <div class="col-md-3">
                                <label for="active">
                                    <input type="checkbox"  name="active" value="1" id="active">
                                Active</label>
                            </div>

                            <div class="col-md-3">
                                <label for="selected">
                                    <input type="checkbox"  name="selected" value="1" id="selected">
                                Selected</label>
                            </div>

                            <div class="col-md-3">
                                <label for="required">
                                    <input type="checkbox"  name="required" value="1" id="required">
                                Required</label>
                            </div>
                         
                            <div class="col-md-3">
                            <label for="position">Position</label>
                                <select id="position" name="position" class="form-control">
                                    <option value="register">Register</option>
                                    <option value="orderform">Order Form</option>
                                    <option value="payment">Payment</option>
                                </select>
                            </div>

                        </div>
                        
                        <hr class="mt-2 mb-3" />
                        <div class="row ">
                            <div class="col-md-12" style="display:flex; align-items: center; justify-content: space-between; margin-bottom: 10px">
                            <div>
                            <label for="contract">Contract</label>
                            </div>
                            <div class="pull-right" title="click to copy" onclick="navigator.clipboard.writeText('@order-information')" style="background-color: #ffbc00; color: black; padding: 3px; border-radius:3px; cursor: pointer">@order-information</div>
                            </div>
                            <div class="col-md-12">
                                <textarea name="contract" id="contract" class="form-control" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="submitBtn">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="confirm_modal" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="material-icons">&#xE5CD;</i>
                    </div>
                    <h4 class="modal-title w-100">Are you sure?</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true"
                        aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="delete-contract btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/trumbowyg.min.js') }}"></script>



    <script>

            var trumbowygConfig = {
                btns: [
                        ['viewHTML'],
                        ['undo', 'redo'], 
                        ['strong', 'em', 'del'],
                        ['link'],
                        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                        ['unorderedList', 'orderedList'],
                        ['horizontalRule'],
                    ]
            };

            var $contractTextArea = $("#contract");

        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });
        $('#contract_form').validate({
            rules: {
                password_confirm: {
                    equalTo: "#password"
                }
            }
        });


       


        var contracts_table = $('#contracts_table').DataTable({
            sDom: '<"top float-right" Bl> <"clear"><"top" <"test">>rt<"bottom" ip><"clear">',
            dom: 'Bfrtip',
            buttons: {
                buttons: [{
                    className: 'btn-primary',
                    text: 'Add New Contract',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add Contract');
                        $('#id').val(-1);
                        $("#name").rules("add", "required");
                        $("#display_order").rules("add", "required");
                        $contractTextArea.html("");
                        $contractTextArea.trumbowyg(trumbowygConfig);
                        $('#edit_modal').modal('show');
                    }
                }],
                dom: {
                    button: {
                        className: 'btn'
                    },
                    buttonLiner: {
                        tag: null
                    }
                }
            },
            pageLength: 50,
            responsive: true,
            processing: true,
            serverSide: true,

            ajax: {
                url: "/api/contracts",
                type: 'get',
            },
            columns: [

                {
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'display_order',
                    name: 'display_order'
                },
                {
                    data: 'position',
                    name: 'position'
                },
                {
                    data: 'active',
                    name: 'active',
                    render: function(data, type, row) {
                        return data == 1 ? "Yes": "No";
                    }
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false
                }
            ],

        });
        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');
            $('#edit_modal_label').text('Edit Contract');
            $('#edit_modal').modal('show');
        });
        $('#edit_modal').on('show.bs.modal', function() {
            var el = $(".edit-item-trigger-clicked");
            var id = el.data('id');
            if (id) {
                $.ajax({
                    url: "/api/contracts/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function(data) {
                     
                        obj = JSON.parse(data);
                        $('#id').val(obj.id);
                        $('#name').val(obj.name)
                        $('#prefix').val(obj.prefix)
                        $('#suffix').val(obj.suffix)
                        $('#position').val(obj.position)
                        $('#display_order').val(obj.display_order)

                        if (obj.active) {
                            $("#active").prop("checked",true);
                        }
                        if (obj.selected) {
                            $("#selected").prop("checked",true);
                        }
                        if (obj.required) {
                            $("#required").prop("checked",true);
                        }
                        $("#name").rules("add", "required");
                        $("#display_order").rules("add", "required");

                        $contractTextArea.html(obj.contract);
                        $contractTextArea.trumbowyg(trumbowygConfig);
                    }
                });
            }
        });


        $('#submitBtn').on('click', function() {
            if ($("#contract_form").valid()) {
                var form = $('#contract_form').serialize();
                $.ajax({
                    url: '/api/contracts',
                    type: 'POST',
                    data: form,
                    success: function(data) {
                        $('#edit_modal').modal('hide');
                        contracts_table.ajax.reload();
                    },
                    error: function(data) {
                        if (data.responseJSON.message) {
                            var errors = $.parseJSON(data.responseText);
                            html = '<div class="alert alert-danger">';
                            $.each(data.responseJSON.message, function(key, value) {
                                html += '<p>' + value[0] + '</p>';
                            });
                            html += '</div>';
                            $('#contracts_result').html(html);
                        }
                        contracts_table.ajax.reload();
                    }
                })
            }
        });

        $('#edit_modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#contract_form").trigger("reset");
            $('#contracts_result').empty();
        });

        $(document).on('click', '.delete', function() {
            $(this).addClass('delete-item-trigger-clicked');
            $('#confirm_modal').modal('show');
        });

        $('.delete-contract').on('click', function(e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: 'api/contracts/' + id,
                type: "DELETE",
                success: function(data) {
                    $('#confirm_modal').modal('hide');
                    contracts_table.ajax.reload();
                },
                error: function(data) {}
            }).done(function() {
                setTimeout(function() {
                    $("#overlay").fadeOut(300);
                }, 500);
            }).fail(function() {
                setTimeout(function() {
                    $("#overlay").fadeOut(300);
                }, 500);
            });
        });
    </script>
@endsection
