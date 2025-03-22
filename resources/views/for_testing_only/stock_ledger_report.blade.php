<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stock Ledger Report</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <div class="container-fluid">
        <div class="row py-3">
            <div class="col-3">
                <select name="warehouse" id="" class="form-control">
                    @foreach ($warehouses as $warehouse)
                        <option value="{{ $warehouse }}">{{ $warehouse }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 py-2">
                <div id="report" style="max-height: 90vh; overflow: auto"></div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="{{ asset('js/core/ajax.min.js') }}"></script> 
    <script src="{{ asset('js/core/jquery.min.js') }}"></script>
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('/js/plugins/bootstrap-notify.js') }}"></script>
    <script>
        function load(warehouse) {
            $.ajax({
                type: 'GET',
                url: '/stock_ledger_report',
                data: { warehouse },
                success: function(response) {
                    $('#report').html(response)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    console.error('Response:', jqXHR.responseText);
                    alert('Error: ' + textStatus + ' - ' + errorThrown + '\n' + jqXHR.responseText);
                }
            });
        }

        const create_stock_recon = (warehouse, qty, item_code, company) => {
            $.ajax({
                type: 'POST',
                url: '/create_stock_recon',
                data: { warehouse, qty, item_code, company },
                success: function(response) {
                    alert('ok')
                    // $('#report').html(response)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    console.error('Response:', jqXHR.responseText);
                    alert('Error: ' + textStatus + ' - ' + errorThrown + '\n' + jqXHR.responseText);
                }
            });
        }

        load()

        $(document).on('change', 'select', function(e){
            e.preventDefault()

            // const warehouse = $('select[name="warehouse"]').val()

            load($(this).val())
        })

        $(document).on('click', '.create-stock-recon', function (e){
            e.preventDefault()

            const qty = $(this).data('quantity');
            const warehouse = $(this).data('warehouse');
            const item_code = $(this).data('item_code');
            const company = $(this).data('company');

            create_stock_recon(warehouse, qty, item_code, company)
        })
    </script>
</body>
</html>