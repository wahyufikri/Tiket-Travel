<!DOCTYPE html>
<html>
<head>
    <title>Midtrans Snap</title>
</head>
<body>
    <button id="pay-button">Bayar Sekarang</button>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result){ console.log('success', result); },
                onPending: function(result){ console.log('pending', result); },
                onError: function(result){ console.log('error', result); },
                onClose: function(){ console.log('customer closed the popup'); }
            });
        };
    </script>
</body>
</html>
