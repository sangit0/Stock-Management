@extends('layouts.app')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>


<div class="row">
   <div class="col-md-12">

      @if (session('message'))
      <div class="alert alert-success">
         {{ session('message') }}
      </div>
      @endif
      <?php
         echo Session::put('message','');
         ?>
          @component('components.widget')
              @slot('title')
                  Setting
              @endslot
              @slot('description')
                  Manage your settings here
              @endslot
              @slot('body')

            <div class="row " >
               <div class="tab-content col-md-8" >

                  <div id="menu1" class="tab-pane active fade in " >
                    <ul class="nav  nav-tabs nav-justified  box box-solid ">
                       <li class="active"><a data-toggle="pill" href="#manageBrand">Manage Brand</a></li>
                       <li><a data-toggle="pill" href="#addBrand">Add New Brand</a></li>
                    </ul>
                    <!-- brand code starts here -->
                    <div class="col-md-9  col-md-offset-2">
                       <div class="box  box-solid">
                          <div class="tab-content">
                             <div id="addBrand" class="tab-pane fade">
                                {!!Form::open(array('url'=>'/save-brand','method'=>'post','files'=>'true'))!!}
                                <table class="table table-bordered table-hover">
                                   <tbody>
                                      <tr>
                                         <td><span  >Brand Name</span></td>
                                         <td><input type="text" name="name" value="" class="span6 " required /></td>
                                      </tr>


                                   </tbody>
                                </table>
                                   <button type="submit" class="btn btn-primary btn-sm">SAVE INFORMATION</button>
                                {!!Form::close()!!}
                             </div>
                             <!-- add brand finished here -->



                             <!-- The manage brand code starts here -->
                             <div id="manageBrand" class="tab-pane fade in active">
                                <table id="brandtbl" class="table table-striped">
                                   <thead>
                                      <tr>
                                         <th><i class="icon-bullhorn"></i>Name</th>
                                         <th><i class=" icon-edit"></i>Status</th>
                                         <th>Action</th>
                                         <th>Edit</th>
                                      </tr>
                                   </thead>
                                   <tbody>
                                      <?php
                                         $manage_brand = DB::table('products_brand')
                                                 ->select('*')
                                                 ->get();
                                             foreach($manage_brand as $v_brand)
                                             {

                                            ?>
                                      <tr>

                                         {!!Form::open(array('url'=>'/update-brand-info','method'=>'post'))!!}
                                        <td>
                                          <input type="hidden" name="ID" style="text-align:center"
                                             value="{{$v_brand->ID}}">

                                             <input type="text" name="name" style="text-align:center"
                                                value="{{$v_brand->name}}" required>
                                          </td>
                                         <td class="hidden-phone"><?php
                                            if($v_brand->publication_status==1)
                                            {
                                                echo 'ON';
                                            }
                                            else{
                                                echo 'OFF';
                                            }

                                            ?></td>
                                         <td>
                                            <?php
                                               if($v_brand->publication_status==1)
                                               {


                                               ?>
                                               <label style="margin-bottom:0px;"class="switch">
                            <a href="{{URL::to('/unpublished-brand/'.$v_brand->ID)}}"><input id="switchMenu" type="checkbox" checked>
                            <span class="slider round"></span></a>
                          </label>
                                            <?php }
                                               elseif($v_brand->publication_status==0){

                                               ?>
                                               <label style="margin-bottom:0px;"class="switch">
                            <a href="{{URL::to('/published-brand/'.$v_brand->ID)}}"><input id="switchMenu"  type="checkbox">
                            <span class="slider round"></span></a>
                          </label>
                                            <?php } ?>
                                         </td>
                                         <td>
                                            <div class="form-actions">
                                               <button type="submit" class="btn alert-danger btn-sm">SAVE</button>
                                            </div>
                                          </td>
                                          {!!Form::close()!!}
                                      </tr>
                                      <?php } ?>
                                   </tbody>
                                </table>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
                 <!-- manage brand finished here -->
                 <!-- </div> -->
                 <!-- The style code finishes here -->

                   <div id="menu2" class="tab-pane fade in " >
                       <ul class="nav  nav-tabs nav-justified  box box-solid ">
                           <li class="active"><a data-toggle="pill" href="#manageStyle">Manage Style</a></li>
                       </ul>
                       <div class="col-md-9 col-md-offset-2">
                           <div class="box box-solid">
                               <div class="tab-content">

                                   <!-- The manage brand code starts here -->
                                   <div id="manageStyle" class="tab-pane fade in active">

                                           <table class="table table-hover">
                                               <tbody>
                                               <tr>
                                                   <td><span  >New Style Name</span></td>
                                                   <td><input type="text" id="stylename" value="" class="span6 " required /></td>
                                               </tr>
                                               <tr>
                                                   <td> <button type="submit" onclick="saveStyle();" class="btn btn-primary btn-sm">SAVE</button></td>
                                                   <td></td>

                                               </tr>
                                               </tbody>
                                           </table>
                                       <table id="styletbl" class="table table-bordered table-striped">
                                           <thead>
                                           <tr>
                                               <th><i class="icon-bullhorn"></i>Name</th>
                                               <th><i class=" icon-edit"></i>Status</th>
                                               <th>Action</th>
                                               <th>Edit</th>
                                           </tr>
                                           </thead>
                                           <tbody>

                                           </tbody>
                                       </table>


                                   </div>

                               </div>
                           </div>
                       </div>
                   </div>

            </div>
            <!-- The currency code finishes here -->

               <div class="tab-content col-md-3 ">
                  <ul class="nav nav-pills nav-stacked box box-solid box-primary">
                     <li class="active"><a data-toggle="pill" href="#menu1">Brand</a></li>
                      <li><a data-toggle="pill" href="#menu2">Styles</a></li>

                  </ul>

               </div>
         </div>
      </div>
   </div>

    @endslot
    @endcomponent

    <script>
        function saveStyle() {
            var style=document.getElementById("stylename").value;
            if(style=="") alert("Enter style name First");
            else {
                $.ajax({
                    data: {data: style},
                    url: '/save-style',
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        showSnakBar();
                        getStyleData();
                    }
                });
            }
        }
        function getStyleData() {
            $.ajax({
                url: '/get-style',
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    var t = $('#styletbl').DataTable();
                    t.clear().draw();

                    $.each(response, function (i, data) {

                        if(data.status==1) {
                            var status ="ON"
                            var button = '<label style="margin-bottom:0px;"class="switch"><a href="unpublished-style/' + data.id + '"><input id="switchMenu" type="checkbox" checked><span class="slider round"></span></a></label>';
                        }
                        else {
                            var status ="OFF"
                            var button = '<label style="margin-bottom:0px;"class="switch"><a href="published-style/' + data.id + '"><input id="switchMenu" type="checkbox"><span class="slider round"></span></a></label>';
                        }
                        var input ='<input id="style'+data.id+'" type="text" name="name" style="text-align:center"\n' +
                             'value="'+data.name+'" class="span6">';
                        var updateBtn = '<button type="submit" onclick="updateStyle('+data.id+')" class="btn btn-success btn-sm">SAVE</button>';


                        t.row.add([
                            input,
                            status,
                            button,
                            updateBtn,


                        ]).draw(true);

                    });

                }
            });
        }
        function updateStyle(id) {
            var style=document.getElementById("style"+id).value;
            var dataPush={
                id:id,
                name:style,
            };
                $.ajax({
                    data: {data: dataPush},
                    url: '/update-style-info',
                    type: 'POST',
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function (response) {
                        showSnakBar();
                        getStyleData();
                    }
                });

        }
        $(function () {
//
            getStyleData();
            $('#brandtbl').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });
            $('#styletbl').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });


        });
    </script>

@endsection
