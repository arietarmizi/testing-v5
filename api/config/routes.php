<?php

use Ramsey\Uuid\Uuid;

$uuidPattern = trim(Uuid::VALID_PATTERN, '^$');

return [
    ''                        => 'site/index',
    'rsa'                     => 'site/rsa',
    'encoded'                 => 'site/encoded',
    'tokopedia/shop/showcase' => 'tokopedia/shop/showcase',

    'auth/register' => 'auth/register',
    'auth/login'    => 'auth/login',

    'account/profile'                       => 'account/profile',
//    ''                                 => 'site/index',
    'dummy'                                 => 'dummy',
//    'tokopedia/shop/showcase'          => 'tokopedia/shop/showcase',

//    TOKOPEDIA PRODUCTS
    'tokopedia/product/product-info-by-id'  => 'tokopedia/product/product-info-by-id',
    'tokopedia/product/product-info-by-sku' => 'tokopedia/product/product-info-by-sku',
    'tokopedia/product/get-variant'         => 'tokopedia/product/get-variant',

    'tokopedia/order/get-single-order' => 'tokopedia/order/get-single-order',

    'product/create'                           => 'product/create',
    'product/<id:' . $uuidPattern . '>'        => 'product/update',
    'product/delete/<id:' . $uuidPattern . '>' => 'product/delete',
    'product/list'                             => 'product/list',
    'product/add-image'                        => 'product/add-image',
    'product/download'                         => 'product/download',
    'product/detail/<id:' . $uuidPattern . '>' => 'product/detail',

    'product-variant/store'                            => 'product-variant/store',
    'product-variant/<id:' . $uuidPattern . '>'        => 'product-variant/update',
    'product-variant/delete/<id:' . $uuidPattern . '>' => 'product-variant/delete',
    'product-variant/list'                             => 'product-variant/list',
	'product-variant/add-image'                        => 'product-variant/add-image',
	'product-variant/detail/<id:' . $uuidPattern . '>' => 'product-variant/detail',

    'product-bundle/store'                            => 'product-bundle/store',
    'product-bundle/<id:' . $uuidPattern . '>'        => 'product-bundle/update',
    'product-bundle/delete/<id:' . $uuidPattern . '>' => 'product-bundle/delete',
    'product-bundle/list'                             => 'product-bundle/list',

    'product-bundle-detail/store'                            => 'product-bundle-detail/store',
    'product-bundle-detail/<id:' . $uuidPattern . '>'        => 'product-bundle-detail/update',
    'product-bundle-detail/delete/<id:' . $uuidPattern . '>' => 'product-bundle-detail/delete',
    'product-bundle-detail/list'                             => 'product-bundle-detail/list',

    'product-promo/store'                            => 'product-promo/store',
    'product-promo/<id:' . $uuidPattern . '>'        => 'product-promo/update',
    'product-promo/delete/<id:' . $uuidPattern . '>' => 'product-promo/delete',
    'product-promo/list'                             => 'product-promo/list',

    'product-discount/store'                            => 'product-discount/store',
    'product-discount/<id:' . $uuidPattern . '>'        => 'product-discount/update',
    'product-discount/delete/<id:' . $uuidPattern . '>' => 'product-discount/delete',
    'product-discount/list'                             => 'product-discount/list',

    'stock-management/store'                            => 'stock-management/store',
    'stock-management/<id:' . $uuidPattern . '>'        => 'stock-management/update',
    'stock-management/delete/<id:' . $uuidPattern . '>' => 'stock-management/delete',
    'stock-management/list'                             => 'stock-management/list',

    'master-status/store'                                => 'master-status/store',
    'master-status/list'                                 => 'master-status/list',
    'master-status/<id:' . $uuidPattern . '>'            => 'master-status/update',
    'master-status/delete/<id:' . $uuidPattern . '>'     => 'master-status/delete',

//    Category
    'category/scrap'                                     => 'category/scrap',
    'category/list'                                      => 'category/list',

//    SUBSCRIPTION TYPE
    'subscription-type/store'                            => 'subscription-type/store',
    'subscription-type/<id:' . $uuidPattern . '>'        => 'subscription-type/update',
    'subscription-type/list'                             => 'subscription-type/list',
    'subscription-type/delete/<id:' . $uuidPattern . '>' => 'subscription-type/delete',

    'subscription/store' => 'subscription/store',
    'subscription/list'  => 'subscription/list',

    'marketplace/store'                            => 'marketplace/store',
    'marketplace/<id:' . $uuidPattern . '>'        => 'marketplace/update',
    'marketplace/delete/<id:' . $uuidPattern . '>' => 'marketplace/delete',
    'marketplace/list'                             => 'marketplace/list',

    'shop/store'                            => 'shop/store',
    'shop/list'                             => 'shop/list',
    'shop/<id:' . $uuidPattern . '>'        => 'shop/update',
    'shop/delete/<id:' . $uuidPattern . '>' => 'shop/delete',

    'order/store'                            => 'order/store',
    'order/list'                             => 'order/list',
    'order/detail/<id:' . $uuidPattern . '>' => 'order/detail',
    'order/<id:' . $uuidPattern . '>'        => 'order/update',
    'order/delete/<id:' . $uuidPattern . '>' => 'order/delete',
    'order/download'                         => 'order/download',

    'order-detail/store'                     => 'order-detail/store',
    'order-detail/list'                      => 'order-detail/list',
    'order-detail/<id:' . $uuidPattern . '>' => 'order-detail/update',

    'order-status/list' => 'order-status/list',

    'warehouse/store'                            => 'warehouse/store',
    'warehouse/list'                             => 'warehouse/list',
    'warehouse/<id:' . $uuidPattern . '>'        => 'warehouse/update',
    'warehouse/delete/<id:' . $uuidPattern . '>' => 'warehouse/delete',

    'customer/store'                            => 'customer/store',
    'customer/list'                             => 'customer/list',
    'customer/<id:' . $uuidPattern . '>'        => 'customer/update',
    'customer/delete/<id:' . $uuidPattern . '>' => 'customer/delete',

    'courier-information/store'                            => 'courier-information/store',
    'courier-information/list'                             => 'courier-information/list',
    'courier-information/<id:' . $uuidPattern . '>'        => 'courier-information/update',
    'courier-information/delete/<id:' . $uuidPattern . '>' => 'courier-information/delete',

    'shipment/download' => 'shipment/download',
    'shipment/list'     => 'shipment/list',
    'shipment/update'   => 'shipment/update'
];
