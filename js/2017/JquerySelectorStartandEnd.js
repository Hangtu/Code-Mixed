if($('#isEditable').val() == "false"){
    var address = [
    '#id_orderbytype_address',
    '#id_billtopaidtotype_address',
    '[id ^=shipto_shipToCodes_][id $=_address]',
    '#docs_address'
    ];

    for (var i = 0 ; i < address.length; i++){
        $(address[i]).removeAttr('disabled');
        $(address[i]).attr('readonly', true);
        $(address[i]).attr('title', $(address[i]).val());
    }
}