showModalAdd = function(){
  $('#modal_add').modal('show');
}

$('.modal').on('shown.bs.modal', function() {
  $(this).find('[autofocus]').focus();
});

initTable = function(){
            table = $("#myTable").on('preXhr.dt', function ( e, settings, data ) {
                
    
                }).on( 'draw.dt', function () {
                    
    
                }).DataTable({
                "columnDefs": [
                    { "width": "5%", "targets": 0, className: "text-start","orderable": false },
                    { "width": "25%", "targets": 1, className: "text-start","orderable": false },
                    { "width": "25%", "targets": 2,className: "text-start","orderable": false },
                    { "width": "25%", "targets": 3, className: "text-start","orderable": false },
                    { "width": "30%", "targets": 4, className: "text-start","orderable": false },
                    
                ],
                'pageLength': 10,
                'processing': true,
                'serverSide': true,
                'serverMethod': 'get',
                'ajax': {
                    'url':"/show",
                    'dataType:':'json',
                    'type':'get',
                },
                'columns': [
                    { 'data': 'no' },
                    { 'data': 'cust_name' },
                    { 'data': 'cust_city' },
                    { 'data': 'cust_country' },
                    { 'data': 'action' },
                       
                ]
            });
            
        }

        initTable();


  $('#saveBtn').on('click', function () {
            var cust_name = $('#cust_name').val();
            var cust_city = $('#cust_city').val();
            var cust_country = $('#cust_country').val();
            var agent_code = $('#agent_code').val();
            $.ajax({
                type: "POST",
                url: "/create",
                dataType: "JSON",
                data: {cust_name: cust_name, cust_city: cust_city, cust_country: cust_country, agent_code: agent_code, },
                success: function (data) {
                    if (data) {
                       
                        let timerInterval
                        Swal.fire({
                            title: 'Memuat Data...',
                            html: 'Tunggu  <b></b>  Detik.',
                            timer: 300,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                    b.textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                            
                        }).then((result) => {
                            Swal.fire(
                                {
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data telah ditambahkan.',
                                    //footer: '<a href="">Why do I have this issue?</a>'
                                }
                                
                                )
                                $('#modalaAdd').on('hidden.bs.modal', function(){
                                    $(this).find('form')[0].reset();
                                 });

                              table.draw(false)
                            })

                             $('#modal_add').modal('hide');
                            
                          } else {
                            Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: 'Ada yang eror!',
                              //footer: '<a href="">Why do I have this issue?</a>'
                            })
                          }
                   
                }
            });
            return false;
        });
    

    $('#tbody').on('click', '.item_hapus', function () {
            var id = $(this).attr('data');
            $('#ModalHapus').modal('show');
            $('[name="kode"]').val(id);
        });

        
    $('#btn_hapus').on('click', function () {
            var kode = $('#textkode').val();
            $.ajax({
                type: "POST",
                url: "/delete",
                dataType: "JSON",
                data: { kode: kode },
                success: function (data) {
                    if (data) {
                        $('#ModalHapus').modal('hide');
                        let timerInterval
                        Swal.fire({
                            title: 'Memuat Data...',
                            html: 'Tunggu  <b></b>  Detik.',
                            timer: 300,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                    b.textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            Swal.fire(
                                {
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data telah dihapus.',
                                    //footer: '<a href="">Why do I have this issue?</a>'
                                }

                            )
                        })

                        table.draw(false)
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Ada yang eror!',
                            //footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }



                    //tampil_data_barang();
                }
            });
            return false;
        });


        $('#tbody').on('click', '.item_edit', function () {
            var cust_code = $(this).attr('data');
            $.ajax({
                type: "GET",
                url: "/" + cust_code + "/select",
                dataType: "JSON",
                data: { cust_code: cust_code },
                success: function (data) {
                    $.each(data, function (cust_code, cust_name, cust_city, cust_country, agent_code ) {
                        $('#ModalEdit').modal('show');
                        $('[name="cust_code"]').val(data[0].cust_code);
                        $('[name="cust_name"]').val(data[0].cust_name);
                        $('[name="cust_city"]').val(data[0].cust_city);
                        $('[name="cust_country"]').val(data[0].cust_country);
                        $('[name="agent_code"]').val(data[0].agent_code);
                        
                    });
                }
            });
            return false;
        });


        $('#btn_update').on('click', function () {
            var cust_code = $('#edit_code').val();
            var cust_name = $('#edit_name').val();
            var cust_city = $('#edit_city').val();
            var cust_country = $('#edit_country').val();
            var agent_code = $('#edit_agent').val();
            
            $.ajax({
                type: "POST",
                url: "/ubah",
                dataType: "JSON",
                data: { cust_code : cust_code, cust_name: cust_name, cust_city: cust_city, cust_country: cust_country, agent_code: agent_code },
                success: function (data) {
                    console.log(data)
                    if (data) {
                        let timerInterval
                        $('#ModalEdit').modal('hide');
                        Swal.fire({
                            title: 'Memuat Data...',
                            html: 'Tunggu  <b></b>  Detik.',
                            timer: 300,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                    b.textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            Swal.fire(
                                {
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data telah diubah.',
                                    //footer: '<a href="">Why do I have this issue?</a>'
                                }

                            )
                            
                        table.draw(false)
                        })
                         

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Ada yang eror!',
                            //footer: '<a href="">Why do I have this issue?</a>'
                        })
                    }
                }
            });
            return false;
        }); 



        $('#tbody').on('click', '.item_detail', function () {
            var cust_code = $(this).attr('data');
            $.ajax({
                type: "GET",
                url: "/" + cust_code + "/detail",
                dataType: "JSON",
                data: { cust_code: cust_code },
                success: function (data) {
                    $.each(data, function (cust_code, cust_name, cust_city, cust_country, agent_name, working_agent, phone_agent) {
                        $('#ModalDetail').modal('show');
                        $('[name="cust_code"]').val(data[0].cust_code);
                        $('[name="cust_name"]').val(data[0].cust_name);
                        $('[name="cust_city"]').val(data[0].cust_city);
                        $('[name="cust_country"]').val(data[0].cust_country);
                        $('[name="agent_name"]').val(data[0].agent_name);
                        $('[name="working_agent"]').val(data[0].working_agent);
                        $('[name="phone_agent"]').val(data[0].phone_agent);
                        
                    });
                }
            });
            return false;
        });
