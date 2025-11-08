@extends('layouts.app')

@section('content')
    <script>
        function getInfo(r) {
            $.ajax({
                url: '/view-supplyer/'+r,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    document.getElementById("name").value = response.name;
                    document.getElementById("ID").value = response.id;
                    document.getElementById("adress").value = response.Adress;
                    document.getElementById("phone").value = response.contact;
                    document.getElementById("balance").value = response.total_balance;
                    document.getElementById("paid").value = response.paid;



                }
            });

        }



    </script>
    <h2>
        Supplier
        <small>Manage supplier</small>
    </h2>
    <hr class="alert-info">
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <?php
    echo Session::put('message','');
    ?>
    @if (session('info'))
        <div class="alert alert-danger">
            {{ session('info') }}
        </div>
    @endif
    <?php
    echo Session::put('info','');
    ?>
    <div class="row">
        @component('components.widget')
            @slot('title')
                Supplier
            @endslot
            @slot('description')
                Manage supplier information
            @endslot
            @slot('body')
                    <button class="btn btn-success" data-toggle="modal" data-target="#addSupplyer"><i class="fa fa-plus-square"></i> Add new supplyer</button>

                    <table  id="sTBL" class="table table-bordered table-hover">
                        <thead>
                        <tr>

                            <th><i class="fa fa-sort"></i> ID </th>
                            <th><i class="fa fa-sort"></i> Name</th>
                            <th><i class="fa fa-sort"></i> Type</th>
                            <th><i class="fa fa-sort"></i> Status</th>
                            <th><i class=""></i> Action</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($supplyer as $supplyer)
                            <tr>
                                <td>{{ $supplyer->id}}</td>
                                <td>{{ $supplyer->name}}</td>
                                @if($supplyer->type==0)
                                    <td>Export</td>
                                @else
                                    <td>Local</td>
                                @endif

                                @if($supplyer->publication_status==1)
                                    <td>

                                        <label style="margin-bottom:0px;"class="switch">
                                            <a href="{{URL::to('/unpublished-supplyer/'.$supplyer->id)}}">
                                                <input id="switchMenu" type="checkbox" checked>
                                                <span class="slider round"></span></a>

                                        </label>
                                    </td>
                                @else
                                    <td>
                                        <label style="margin-bottom:0px;"class="switch">
                                            <a href="{{URL::to('/published-supplyer/'.$supplyer->id)}}">
                                                <input id="switchMenu" type="checkbox" >
                                                <span class="slider round"></span></a>

                                        </label>

                                    </td>
                                @endif
                                <td>

                                    <button class="btn btn-sm btn-success" onclick="getInfo('{{$supplyer->id}}');" data-toggle="modal" data-target="#editEmp"><i class="fa fa-pencil"></i> Edit</button>

                                </td>
                            </tr>
                        @endforeach

                        </tbody>

                    </table>

                @endslot
        @endcomponent
                </div>

    <div class="modal fade" id="editEmp">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit supplyer</h4>
                </div>
                <div class="modal-body">
                    <table  class="table">
                        <form method="POST" action="/update-supplyer">

                            {{ csrf_field() }}


                            <tbody>
                            <tr>
                                <td></td>
                                <td>Select type:</td>


                                <td><select name="cat" required>
                                        <option value="-1">Select...</option>
                                        <option value="0">Export</option>
                                        <option value="1">Local</option>

                                    </select></td>

                            </tr>
                            <tr>
                                <td></td>
                                <td>Name</td>


                                <td><input id="name" name="name" type="text"  required/></td>
                               <input id="ID" name="ID" type="hidden"  required/>


                            </tr>


                            <tr>
                                <td></td>
                                <td>Adress</td>


                                <td><input type="text" name="adress" id="adress" /></td>

                            </tr>
                            <tr>
                                <td></td>
                                <td>Contact</td>


                                <td><input type="number" name="phone" id="phone" /></td>

                            </tr>
                            <tr>
                                <td></td>
                                <td>Balance</td>


                                <td><input type="number" name="balance" id="balance" /></td>

                            </tr>
                            <tr>
                                <td></td>
                                <td>Paid balance</td>


                                <td><input type="number" name="paid" id="paid" /></td>

                            </tr>
                            <tr>
                                <td></td>

                                <td>
                                    <button type="submit" class="btn btn-success btn-sm">SAVE</button>
                                </td>
                                <td></td>

                            </tr>

                        </form>



                        </tbody>



                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="addSupplyer">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add new supplyer</h4>
                </div>
                <div class="modal-body">
                    <table  class="table">
                        <form method="POST" action="/save-supplyer">
                        {{ csrf_field() }}
                        <tbody>
                        <tr>
                            <td></td>
                            <td>Select type:</td>
                            <td><select name="cat" class="form-control" required>
                                    <option value="-1">Select...</option>

                                    <option value="0">Export</option>
                                    <option value="1">Local</option>

                                </select></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Name</td>


                            <td><input type="text" name="name" required/></td>

                        </tr>


                        <tr>
                            <td></td>
                            <td>Adress</td>


                            <td><input type="text" name="adress" /></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Contact</td>


                            <td><input type="number" name="phone" /></td>

                        </tr>

                        <tr>
                            <td></td>
                            <td>Balance</td>


                            <td><input type="number" name="balance" value="0" id="balance" /></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Paid balance</td>


                            <td><input type="number" name="paid" value="0" id="paid" /></td>

                        </tr>
                        <tr>
                            <td></td>

                            <td>
                            <button type="submit" class="btn btn-success btn-sm">SAVE</button>
                             </td>
                            <td></td>

                        </tr>

                        </form>



                        </tbody>



                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <script>
        $(function () {

            $('#sTBL').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            })
        })
    </script>


@endsection
