
// $( document ).ready(function() {
    
// });

    // function convertToRupiah(angka) {
    //     var rupiah = '';
    //     var angkarev = angka.toString().split('').reverse().join('');
    //     for (var i = 0; i < angkarev.length; i++)
    //         if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
    //     return rupiah.split('', rupiah.length - 1).reverse().join('');
    // }

    $(document).ready(function(){
        var elts = document.getElementsByClassName('rupiah-text');
        for (var i = 0; i < elts.length; ++i) {
            let rp =  elts[i].innerHTML ;  
            rp = formatRupiah(rp,'Rp.') ;
            elts[i].innerHTML = rp;
        }
    })
    function rupiahInAngka(val){
        val = val.toString();
        val = formatRupiah(val,"Rp. ")
    }
    function formatRupiah(angka, prefix)
    {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split    = number_string.split(','),
            sisa     = split[0].length % 3,
            rupiah     = split[0].substr(0, sisa),
            ribuan     = split[0].substr(sisa).match(/\d{3}/gi);
            
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp.' + rupiah : '');
    }
    // var tanpa_rupiah = document.getElementById('pinjaman');
    // tanpa_rupiah.addEventListener('keyup', function(e)
    // {
    //     tanpa_rupiah.value = formatRupiah(this.value);
    // });
    
    function jumPinjaman (val){
        let pinjaman = document.getElementById('pinjaman');
        // let jum = parseFloat(pinjaman.value.replace(/\./g,"")) ;
        pinjaman.value = formatRupiah(val);
    }

    function toFloat(){
        let pinjaman = document.getElementById('angka');
        let jum = parseFloat(pinjaman.value.replace(/\./g,"")) ;
        pinjaman.value = jum;
    }



// function convertToRupiah(angka) {
//     var rupiah = '';
//     var angkarev = angka.toString().split('').reverse().join('');
//     for (var i = 0; i < angkarev.length; i++)
//         if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
//     return rupiah.split('', rupiah.length - 1).reverse().join('');
// }

// function formatRupiah(angka, prefix){
//     var number_string = angka.replace(/[^,\d]/g, '').toString(),
//     split   		= number_string.split(','),
//     sisa     		= split[0].length % 3,
//     rupiah     		= split[0].substr(0, sisa),
//     ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

//     // tambahkan titik jika yang di input sudah menjadi angka ribuan
//     if(ribuan){
//         separator = sisa ? '.' : '';
//         rupiah += separator + ribuan.join('.');
//     }

//     rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
//     return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
// }

