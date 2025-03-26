<?php

return [
    'currencies' => [
        'TRY' => 949,
        'EUR' => 978,
        'USD' => 840,
    ],
    'banks' => [
		'akbank-pos' => [
             'name' => 'Akbank T.A.Ş.',
             'class' => SinyorPos\Gateways\AkbankPos::class,
             'gateway_endpoints'  => [
                'payment_api'     => 'https://api.akbank.com/api/v1/payment/virtualpos',
                'gateway_3d'      => 'https://virtualpospaymentgateway.akbank.com/securepay',
                'gateway_3d_host' => 'https://virtualpospaymentgateway.akbank.com/payhosting',
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
		'akbankv3' => [
             'name' => 'Akbank T.A.Ş.',
             'class' => SinyorPos\Gateways\EstV3Pos::class,
             'gateway_endpoints'  => [
                'payment_api'     => 'https://www.sanalakpos.com/fim/api',
                'gateway_3d'      => 'https://www.sanalakpos.com/fim/est3Dgate',
                'gateway_3d_host' => 'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate',
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
		'akbank' => [
             'name' => 'Akbank T.A.Ş.',
             'class' => SinyorPos\Gateways\EstV3Pos::class,
             'gateway_endpoints'  => [
                'payment_api'     => 'https://www.sanalakpos.com/fim/api',
                'gateway_3d'      => 'https://www.sanalakpos.com/fim/est3Dgate',
                'gateway_3d_host' => 'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate',
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
		 'tosla' => [
             'name' => 'AkÖde A.Ş.',
             'class' => SinyorPos\Gateways\ToslaPos::class,
             'gateway_endpoints'  => [
                'payment_api'     => 'https://entegrasyon.tosla.com/api/Payment',
                'gateway_3d'      => 'https://entegrasyon.tosla.com/api/Payment/ProcessCardForm',
                'gateway_3d_host' => 'https://entegrasyon.tosla.com/api/Payment/threeDSecure',
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
		'finansbank' => [
			'name' => 'QNB Finansbank',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://www.fbwebpos.com/fim/api',
                'gateway_3d'      => 'https://www.fbwebpos.com/fim/est3dgate',
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
		'halkbank' => [
			'name' => 'Halkbank',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://sanalpos.halkbank.com.tr/fim/api',
                'gateway_3d'      => 'https://sanalpos.halkbank.com.tr/fim/est3dgate',
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
		'teb' => [
			'name' => 'TEB',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://sanalpos.teb.com.tr/fim/api',
                'gateway_3d'      => 'https://sanalpos.teb.com.tr/fim/est3Dgate',
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
		'isbank' => [
			'name' => 'İşbank T.A.Ş.',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://sanalpos.isbank.com.tr/fim/api',
                'gateway_3d'      => 'https://sanalpos.isbank.com.tr/fim/est3Dgate',
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
		'sekerbank' => [
			'name' => 'Şeker Bank',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://sanalpos.sekerbank.com.tr/fim/api',
                'gateway_3d'      => 'https://sanalpos.sekerbank.com.tr/fim/est3Dgate',
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
		'yapikredi' => [
			'name' => 'Yapıkredi',
			'class' => SinyorPos\Gateways\PosNet::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://posnet.yapikredi.com.tr/PosnetWebService/XML',
                'gateway_3d'      => 'https://posnet.yapikredi.com.tr/3DSWebService/YKBPaymentService',
            ],
			'accounts' => [
				'test' => [
					'merchant_number' => '',
					'posnet_id' => '',
					'username' => '',
					'password' => '',
					'enc_key' => '',
					'terminal_number' => '',
				],
				'production' => [
					'merchant_number' => '',
					'posnet_id' => '',
					'username' => '',
					'password' => '',
					'enc_key' => '',
					'terminal_number' => '',
				],
			],
		],
		'albaraka' => [
			'name' => 'Albaraka',
			'class' => SinyorPos\Gateways\PosNetV1Pos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://epos.albarakaturk.com.tr/ALBMerchantService/MerchantJSONAPI.svc',
                'gateway_3d'      => 'https://epos.albarakaturk.com.tr/ALBSecurePaymentUI/SecureProcess/SecureVerification.aspx',
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
		'garanti' => [
			'name' => 'Garanti',
			'class' => SinyorPos\Gateways\GarantiPos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://sanalposprov.garanti.com.tr/VPServlet',
                'gateway_3d'      => 'https://sanalposprov.garanti.com.tr/servlet/gt3dengine',
            ],
			'accounts' => [
				'test' => [
					'client_id' => '',
					'username' => '',
					'password' => '',
					'store_key' => '',
					'terminal_number' => '',
				],
				'production' => [
					'client_id' => '',
					'username' => '',
					'password' => '',
					'store_key' => '',
					'terminal_number' => '',
				],
			],
		],
		'qnbfinansbank-payfor' => [
			'name' => 'QNBFinansbank-PayFor',
			'class' => SinyorPos\Gateways\PayForPos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://vpos.qnbfinansbank.com/Gateway/XMLGate.aspx',
                'gateway_3d'      => 'https://vpos.qnbfinansbank.com/Gateway/Default.aspx',
                'gateway_3d_host' => 'https://vpos.qnbfinansbank.com/Gateway/3DHost.aspx',
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
		'vakifbank' => [
			'name' => 'Vakıfbank (VPOS)',
			'class' => SinyorPos\Gateways\PayFlexV4Pos::class,
			'gateway_endpoints'  => [
                'payment_api'     => 'https://onlineodeme.vakifbank.com.tr:4443/VposService/v3/Vposreq.aspx',
                'gateway_3d'      => 'https://3dsecure.vakifbank.com.tr:4443/MPIAPI/MPI_Enrollment.aspx',
                'query_api'       => 'https://onlineodeme.vakifbank.com.tr:4443/UIService/Search.aspx',
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
	    'ziraat-vpos' => [
		    'name' => 'Ziraat Bankası',
		    'class' => SinyorPos\Gateways\PayFlexV4Pos::class,
		    'gateway_endpoints'  => [
                'payment_api'     => 'https://sanalpos.ziraatbank.com.tr/v4/v3/Vposreq.aspx',
                'gateway_3d'      => 'https://mpi.ziraatbank.com.tr/Enrollment.aspx',
                'query_api'       => 'https://sanalpos.ziraatbank.com.tr/v4/UIWebService/Search.aspx',
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
		'ziraat-estpos' => [
		    'name' => 'Ziraat Bankası',
		    'class' => SinyorPos\Gateways\EstV3Pos::class,
		    'gateway_endpoints'  => [
                'payment_api'     => 'https://sanalpos2.ziraatbank.com.tr/fim/api',
                'gateway_3d'      => 'https://sanalpos2.ziraatbank.com.tr/fim/est3Dgate',
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
		'vakifbank-cp' => [
			'name' => 'Vakıfbank (PayFlex Common Payment)',
			'class' => SinyorPos\Gateways\PayFlexCPV4Pos::class,
			'gateway_endpoints'  => [
                'payment_api' => 'https://cpweb.vakifbank.com.tr/CommonPayment/api/VposTransaction',
                'gateway_3d'  => 'https://cpweb.vakifbank.com.tr/CommonPayment/api/RegisterTransaction',
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
	    'denizbank' => [
		    'name' => 'Denizbank',
		    'class' => SinyorPos\Gateways\InterPos::class,
		    'gateway_endpoints'  => [
                'payment_api'     => 'https://inter-vpos.com.tr/mpi/Default.aspx',
                'gateway_3d'      => 'https://inter-vpos.com.tr/mpi/Default.aspx',
                'gateway_3d_host' => 'https://inter-vpos.com.tr/mpi/3DHost.aspx',
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
	    'kuveytpos' => [
		    'name' => 'Kuveyt Türk',
		    'class' => SinyorPos\Gateways\KuveytPos::class,
		    'gateway_endpoints'  => [
                'payment_api' => 'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home',
                'gateway_3d'  => 'https://sanalpos.kuveytturk.com.tr/ServiceGateWay/Home/ThreeDModelPayGate',
                'query_api'   => 'https://boa.kuveytturk.com.tr/BOA.Integration.WCFService/BOA.Integration.VirtualPos/VirtualPosService.svc?wsdl',
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
		'vakif-katilim' => [
		    'name' => 'Vakıf Katılım',
		    'class' => SinyorPos\Gateways\VakifKatilimPos::class,
		    'gateway_endpoints'  => [
                'payment_api' => 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home',
                'gateway_3d'  => 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/Home/ThreeDModelPayGate',
                'gateway_3d_host' => 'https://boa.vakifkatilim.com.tr/VirtualPOS.Gateway/CommonPaymentPage/CommonPaymentPage',
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
		'param-pos' => [
		    'name' => 'TURK Elektronik Para A.Ş',
		    'class' => SinyorPos\Gateways\ParamPos::class,
		    'gateway_endpoints'  => [	
                'payment_api'     => 'https://posws.param.com.tr/turkpos.ws/service_turkpos_prod.asmx',
                'payment_api_2'   => 'https://pos.param.com.tr/Tahsilat/to.ws/Service_Odeme.asmx',
                'gateway_3d_host' => 'https://pos.param.com.tr/Tahsilat/Default.aspx',
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
	    ]
    ],
];
