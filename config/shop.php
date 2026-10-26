<?php

return [
    'delivery_fee' => (int) round((float) env('SHOP_DELIVERY_FEE', 0) * 100) / 100,
];