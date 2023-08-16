<?php

return [
    'currencies' => [
        'TRY' => 949,
        'EUR' => 978,
        'USD' => 840,
    ],
    'banks' => [
        'halkbank' => [
            'name' => 'Halkbank',
            'class' => EceoPos\Gateways\ESTVirtualPos::class,
            'urls' => [
                'production' => 'https://sanalpos.halkbank.com.tr/fim/api',
                'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
                'gateway' => [
                    'production' => 'https://sanalpos.halkbank.com.tr/fim/est3dgate',
                    'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
                ],
            ],
            'accounts' => [
                'test' => [
                    'client_id' => '500100000',
                    'username' => 'HALKBANK',
                    'password' => 'HALKBANK05',
                    'store_key' => '123456',
                ],
                'production' => [
                    'client_id' => '',
                    'username' => '',
                    'password' => '',
                    'store_key' => '',
                ],
            ],
        ],
        'vakifbank' => [
            'name' => 'VakifBank-VPOS',
            'class' => EceoPos\Gateways\VakifBankVirtualPos::class,
            'urls' => [
                'production' => 'https://onlineodeme.vakifbank.com.tr:4443/VposService/v3/Vposreq.aspx',
                'test' => 'https://onlineodemetest.vakifbank.com.tr:4443/VposService/v3/Vposreq.aspx',
                'gateway' => [
                    'production' => 'https://3dsecure.vakifbank.com.tr:4443/MPIAPI/MPI_Enrollment.aspx',
                    'test' => 'https://3dsecuretest.vakifbank.com.tr:4443/MPIAPI/MPI_Enrollment.aspx',
                ],
            ],
            'accounts' => [
                'test' => [
                    'client_id' => '',
                    'username' => '',
                    'password' => '',
                    'store_key' => '',
                ],
                'production' => [
                    'client_id' => '',
                    'username' => '',
                    'password' => '',
                    'store_key' => '',
                ],
            ],
        ],
    ],
];
