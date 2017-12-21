function deleteObj(obj,url,text) {
    $.confirm({
        icon: 'fa fa-warning',
        title: 'Xác nhận xóa',
        content: 'Bạn có chắc muốn xóa <strong>'+text+'</strong> ?',
        type: 'red',
        theme: 'light',
        typeAnimated: true,
        buttons: {
            ok: {
                text: 'Xóa',
                btnClass:'btn-red',
                action:function () {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        dataType:'json',
                        success: function(res) {
                            if(res.status){
                                Notify('Thông báo',res.stt,'success');
                                $(obj).parent().parent().fadeOut('fast');
                            }
                            else
                                Notify('Lỗi',res.stt,'error');
                        },
                        error:function(e){
                            Notify('Lỗi',e.statusText,'error');
                        }
                    });
                }
            },
            close: {
                text: 'Đóng'
            }
        }
    });
}

function recharge(customer_id) {
    $.confirm({
        title: 'Nạp tiền',
        content: '' +
        '<form action="" class="formName">' +
        '<div class="form-group">' +
        '<label>Số tiền cần nạp</label>' +
        '<input type="number" placeholder="10000" class="form-control" id="amount" value="0" autofocus required />' +
        '</div>' +
        '</form>',
        buttons: {
            formSubmit: {
                text: 'Nạp',
                keys: ['enter'],
                btnClass: 'btn-blue',
                action: function () {
                    var price = this.$content.find('#amount').val();
                    if(price==0)
                    {
                        $.alert('Mệnh giá nạp không hợp lệ');
                        return false;
                    }
                    $.ajax({
                        url: 'customer/'+customer_id+'/recharge',
                        type: 'PUT',
                        dataType:'json',
                        data:{price:price},
                        success: function(res) {
                            $.alert(res.stt);
                        },
                        error:function(e){
                            $.alert("Lỗi: "+e.statusText);
                        }
                    });
                }
            },
            cancel: {
                text: 'Đóng',
                keys: ['esc']
            },
        },
        onContentReady: function () {
            var jc = this;
            this.$content.find('form').on('submit', function (e) {
                e.preventDefault();
                jc.$$formSubmit.trigger('click');
            });
        }
    });
}
function view_detail(id) {
    $.get('customer/'+id,function(data){
        $('#view_detail').html(data);
        $('.modal-title').html('Chi tiết khách hàng');
        $('#modal-id').modal('show');
    });
}
function payment(id) {
    $.get('ajax/customer/'+id+'/payment',function(data){
        $('#view_detail').html(data);
        $('.modal-title').html('Nội dung thanh toán');
        $('#modal-id').modal('show');
    });
}

function format_curency(a) {
    return a.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
}