<?php
return [
    'module' => [
        [
            'title' => 'Tổng quan',
            'icon' => 'fa fa-database',
            'name' => ['dashboard'],
            'route' => 'dashboard/index',
            'class' => 'special'
        ],
        [
            'title' => 'Thống Kê',
            'icon' => 'fa fa-money',
            'name' => ['report'],
            'subModule' => [
                [
                    'title' => 'Theo Thời Gian',
                    'route' => 'report/time'
                ],
                [
                    'title' => 'Theo Sản Phẩm',
                    'route' => 'report/product'
                ],
                // [
                //     'title' => 'Theo Nguồn Khách',
                //     'route' => 'report/customer'
                // ],
            ]
        ],
        [
            'title' => 'QL Sản Phẩm',
            'icon' => 'fa fa-cube',
            'name' => ['product', 'attribute'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Sản Phẩm',
                    'route' => 'product/catalogue/index'
                ],
                [
                    'title' => 'QL Sản Phẩm',
                    'route' => 'product/index'
                ],
                [
                    'title' => 'QL Loại Thuộc Tính',
                    'route' => 'attribute/catalogue/index'
                ],
                [
                    'title' => 'QL Thuộc Tính',
                    'route' => 'attribute/index'
                ],

            ]
        ],
        [
            'title' => 'QL Kho Hàng',
            'icon' => 'fa fa-archive',
            'name' => ['supplier', 'purchase-order', 'stock'],
            'subModule' => [
                [
                    'title' => 'QL Nhà Cung Cấp',
                    'route' => 'supplier/index'
                ],
                [
                    'title' => 'QL Nhập Hàng',
                    'route' => 'purchase-order/index'
                ],
                [
                    'title' => 'QL Tồn Kho',
                    'route' => 'stock/inventory/index'
                ],
                [
                    'title' => 'Kiểm Kê Kho',
                    'route' => 'stock/stock-taking/index'
                ],
                [
                    'title' => 'Báo Cáo & Phân Tích',
                    'route' => 'stock/report/index'
                ],
            ]
        ],
        [
            'title' => 'QL Đơn Hàng',
            'icon' => 'fa fa-shopping-bag',
            'name' => ['order'],
            'subModule' => [
                [
                    'title' => 'QL Đơn Hàng',
                    'route' => 'order/index'
                ],
            ]
        ],
        [
            'title' => 'QL Marketing',
            'icon' => 'fa fa-money',
            'name' => ['promotion', 'source', 'slide'],
            'subModule' => [
                [
                    'title' => 'QL Khuyến mại',
                    'route' => 'promotion/index'
                ],
                // [
                //     'title' => 'QL Nguồn Khách',
                //     'route' => 'source/index'
                // ],
                [
                    'title' => 'QL Banner & Slide',
                    'route' => 'slide/index'
                ],
            ]
        ],
        [
            'title' => 'QL Bài viết',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Bài Viết',
                    'route' => 'post/catalogue/index'
                ],
                [
                    'title' => 'QL Bài Viết',
                    'route' => 'post/index'
                ]
            ]
        ],
        [
            'title' => 'QL Bình Luận',
            'icon' => 'fa fa-comment',
            'name' => ['reviews'],
            'subModule' => [
                [
                    'title' => 'QL Bình Luận',
                    'route' => 'review/index'
                ]
            ]
        ],
        [
            'title' => 'QL Nhóm Khách Hàng',
            'icon' => 'fa fa-user',
            'name' => ['customer'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Khách hàng',
                    'route' => asset('customer/catalogue/index')
                ],
                [
                    'title' => 'QL Khách hàng',
                    'route' => 'customer/index'
                ],
            ]
        ],
        [
            'title' => 'QL Nhóm Thành Viên',
            'icon' => 'fa fa-user',
            'name' => ['user', 'permission'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Thành Viên',
                    'route' => 'user/catalogue/index'
                ],
                [
                    'title' => 'QL Thành Viên',
                    'route' => 'user/index'
                ],
                [
                    'title' => 'QL Quyền',
                    'route' => 'permission/index'
                ]
            ]
        ],
        [
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-cogs',
            'name' => ['language', 'generate', 'system', 'widget', 'menu', 'slide'],
            'subModule' => [
                [
                    'title' => 'Cài đặt Menu',
                    'route' => 'menu/index'
                ],
                [
                    'title' => 'Quản lý Widget',
                    'route' => 'widget/index'
                ],
                [
                    'title' => 'Cấu hình hệ thống',
                    'route' => 'system/index'
                ],

            ]
        ],
    ],
];
