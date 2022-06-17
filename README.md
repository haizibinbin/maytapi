# Maytapi PHP SDK

### 用法

```php
<?php

require_once 'vendor/autoload.php';

$product_id = 'xxxxxxxxxx';
$token = 'xxxxxxxxxx';
$phone_id = 'xxxxxx';
$maytapi = new \Hbb\Maytapi\Maytapi($product_id, $token);
$data = $maytapi->status($phone_id);
print_r($data);

```

