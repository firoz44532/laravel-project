Add-Type -AssemblyName System.Drawing
$bmp = New-Object System.Drawing.Bitmap 400,400
$graphics = [System.Drawing.Graphics]::FromImage($bmp)
$graphics.Clear([System.Drawing.Color]::LightGray)
$font = New-Object System.Drawing.Font('Arial',20)
$brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::DarkGray)
$text = 'Product Image'
$textSize = $graphics.MeasureString($text, $font)
$x = (400 - $textSize.Width) / 2
$y = (400 - $textSize.Height) / 2
$graphics.DrawString($text, $font, $brush, $x, $y)
$bmp.Save('f:/xampp/htdocs/ecomerce_e/public/images/placeholder-product.jpg', [System.Drawing.Imaging.ImageFormat]::Jpeg)
$graphics.Dispose()
$bmp.Dispose()
Write-Host 'JPEG image created successfully'
