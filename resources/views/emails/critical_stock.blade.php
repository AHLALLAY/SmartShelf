<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Critical Stock Alert</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <tr>
            <td style="padding: 20px 30px;">
                <h1 style="color: #dc2626; font-size: 24px; margin-bottom: 16px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px;">Critical Stock Alert</h1>
                
                @if (!empty($critiqueProducts))
                    <div style="margin-bottom: 24px; background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 4px;">
                        <h2 style="color: #b45309; font-size: 18px; margin-top: 0; margin-bottom: 12px;">Critical Stock Produits :</h2>
                        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
                            @foreach ($critiqueProducts as $product)
                                <tr style="border-bottom: 1px solid #fef3c7;">
                                    <td style="font-weight: 500; color: #1f2937;">{{ $product->name }}</td>
                                    <td align="right"><span style="background-color: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 12px; font-size: 14px;">Stock: {{ $product->quantityAvailable }}</span></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif

                @if (!empty($outOfStockProducts))
                    <div style="background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 4px;">
                        <h2 style="color: #b91c1c; font-size: 18px; margin-top: 0; margin-bottom: 12px;">Out of stock Produits :</h2>
                        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
                            @foreach ($outOfStockProducts as $product)
                                <tr style="border-bottom: 1px solid #fecaca;">
                                    <td style="font-weight: 500; color: #1f2937;">{{ $product->name }}</td>
                                    <td align="right"><span style="background-color: #fecaca; color: #991b1b; padding: 4px 8px; border-radius: 12px; font-size: 14px;">Stock: {{ $product->quantityAvailable }}</span></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
                
                <div style="margin-top: 24px; text-align: center; font-size: 14px; color: #6b7280; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                    <p>Ce message a été généré automatiquement. Veuillez ne pas y répondre.</p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>