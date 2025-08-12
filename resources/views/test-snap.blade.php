<html>
<head>
    <title>Test Snap Payment</title>
</head>
<body>
    <button id="pay-button">Bayar Sekarang</button>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){
                    alert("Pembayaran Berhasil!");
                    console.log(result);
                },
                onPending: function(result){
                    alert("Menunggu Pembayaran!");
                    console.log(result);
                },
                onError: function(result){
                    alert("Pembayaran Gagal!");
                    console.log(result);
                },
                onClose: function(){
                    alert('Kamu menutup popup tanpa menyelesaikan pembayaran');
                }
            });
        };
    </script>
</body>
</html>
