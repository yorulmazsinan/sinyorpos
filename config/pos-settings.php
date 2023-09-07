<?php

return [
    'currencies' => [ // Para birimleri
        'TRY' => 949,
        'EUR' => 978,
        'USD' => 840,
    ],
    'banks' => [ // Bankalar
		'akbankv3' => [
             'name' => 'Akbank T.A.Ş.',
             'class' => SinyorPos\Gateways\EstV3Pos::class,
             'urls' => [
                 'production' => 'https://www.sanalakpos.com/fim/api',
                 'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
                 'gateway' => [
	                 'production' => 'https://www.sanalakpos.com/fim/est3Dgate',
	                 'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
                 ],
                 'gateway_3d_host' => [
	                 'production' => 'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate',
	                 'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
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
		'akbank' => [
             'name' => 'Akbank T.A.Ş.',
             'class' => SinyorPos\Gateways\EstPos::class,
             'urls' => [
                 'production' => 'https://www.sanalakpos.com/fim/api',
                 'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
                 'gateway' => [
	                 'production' => 'https://www.sanalakpos.com/fim/est3Dgate',
	                 'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
                 ],
                 'gateway_3d_host' => [
	                 'production' => 'https://sanalpos.sanalakpos.com.tr/fim/est3Dgate',
	                 'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
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
		'finansbank' => [
			'name' => 'QNB Finansbank',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
			'urls' => [
				'production' => 'https://www.fbwebpos.com/fim/api',
				'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
				'gateway' => [
					'production' => 'https://www.fbwebpos.com/fim/est3dgate',
					'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
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
		'halkbank' => [
			'name' => 'Halkbank',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
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
			'urls' => [
				'production' => 'https://sanalpos.teb.com.tr/fim/api',
				'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
				'gateway' => [
					'production' => 'https://sanalpos.teb.com.tr/fim/est3Dgate',
					'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
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
		'isbank' => [
			'name' => 'İşbank T.A.Ş.',
			'class' => SinyorPos\Gateways\EstPos::class,
			'urls' => [
				'production' => 'https://spos.isbank.com.tr/fim/api',
				'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
				'gateway' => [
					'production' => 'https://spos.isbank.com.tr/fim/est3Dgate',
					'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
				],
				'gateway_3d_host' => [
					'production' => 'https://spos.isbank.com.tr/fim/est3Dgate',
					'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
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
		'sekerbank' => [
			'name' => 'Şeker Bank',
			'class' => SinyorPos\Gateways\EstV3Pos::class,
			'urls' => [
				'production' => 'https://sanalpos.sekerbank.com.tr/fim/api',
				'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
				'gateway' => [
					'production' => 'https://sanalpos.sekerbank.com.tr/fim/est3Dgate',
					'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
				],
				'gateway_3d_host' => [
					'production' => 'https://sanalpos.sekerbank.com.tr/fim/est3Dgate',
					'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
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
		'yapikredi' => [
			'name' => 'Yapıkredi',
			'class' => SinyorPos\Gateways\PosNet::class,
			'urls' => [
				'production' => 'https://posnet.yapikredi.com.tr/PosnetWebService/XML',
				'test' => 'https://setmpos.ykb.com/PosnetWebService/XML',
				'gateway' => [
					'production' => 'https://posnet.yapikredi.com.tr/3DSWebService/YKBPaymentService',
					'test' => 'https://setmpos.ykb.com/3DSWebService/YKBPaymentService',
				],
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
			'urls'  => [
				'production' => 'https://epos.albarakaturk.com.tr/ALBMerchantService/MerchantJSONAPI.svc',
				'test' => 'https://epostest.albarakaturk.com.tr/ALBMerchantService/MerchantJSONAPI.svc',
				'gateway' => [
					'production' => 'https://epos.albarakaturk.com.tr/ALBSecurePaymentUI/SecureProcess/SecureVerification.aspx',
					'test' => 'https://epostest.albarakaturk.com.tr/ALBSecurePaymentUI/SecureProcess/SecureVerification.aspx',
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
		'garanti' => [
			'name' => 'Garanti',
			'class' => SinyorPos\Gateways\GarantiPos::class,
			'urls' => [
				'production' => 'https://sanalposprov.garanti.com.tr/VPServlet',
				'test' => 'https://sanalposprovtest.garanti.com.tr/VPServlet',
				'gateway' => [
					'production' => 'https://sanalposprov.garanti.com.tr/servlet/gt3dengine',
					'test' => 'https://sanalposprovtest.garanti.com.tr/servlet/gt3dengine',
				],
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
			'name' => 'QNB Finansbank (PayFor)',
			'class' => SinyorPos\Gateways\PayForPos::class,
			'urls' => [
				'production' => 'https://vpos.qnbfinansbank.com/Gateway/XMLGate.aspx',
				'test' => 'https://vpostest.qnbfinansbank.com/Gateway/XMLGate.aspx',
				'gateway' => [
					'production' => 'https://vpos.qnbfinansbank.com/Gateway/Default.aspx',
					'test' => 'https://vpostest.qnbfinansbank.com/Gateway/Default.aspx',
				],
				'gateway_3d_host' => [
					'production' => 'https://vpos.qnbfinansbank.com/Gateway/3DHost.aspx',
					'test' => 'https://vpostest.qnbfinansbank.com/Gateway/3DHost.aspx',
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
		'vakifbank' => [
			'name' => 'Vakıfbank (VPOS)',
			'class' => SinyorPos\Gateways\PayFlexV4Pos::class,
			'urls' => [
				'production' => 'https://onlineodeme.vakifbank.com.tr:4443/VposService/v3/Vposreq.aspx',
				'test' => 'https://onlineodemetest.vakifbank.com.tr:4443/VposService/v3/Vposreq.aspx',
				'gateway' => [
					'production' => 'https://3dsecure.vakifbank.com.tr:4443/MPIAPI/MPI_Enrollment.aspx',
					'test' => 'https://3dsecuretest.vakifbank.com.tr:4443/MPIAPI/MPI_Enrollment.aspx',
				],
				'query'       => [
					// todo update with the correct ones
					'production' => 'https://sanalpos.vakifbank.com.tr/v4/UIWebService/Search.aspx',
					'test' => 'https://sanalpos.vakifbank.com.tr/v4/UIWebService/Search.aspx',
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
	    'ziraat-vpos' => [
		    'name' => 'Ziraat Bankası',
		    'class' => SinyorPos\Gateways\PayFlexV4Pos::class,
		    'urls' => [
			    'production' => 'https://sanalpos.ziraatbank.com.tr/v4/v3/Vposreq.aspx',
			    'test' => 'https://preprod.payflex.com.tr/Ziraatbank/VposWeb/v3/Vposreq.aspx',
			    'gateway' => [
				    'production' => 'https://mpi.ziraatbank.com.tr/Enrollment.aspx',
				    'test' => 'https://preprod.payflex.com.tr/ZiraatBank/MpiWeb/MPI_Enrollment.aspx',
			    ],
			    'query' => [
				    'production' => 'https://sanalpos.ziraatbank.com.tr/v4/UIWebService/Search.aspx',
				    // todo update with the correct one
				    'test' => 'https://sanalpos.ziraatbank.com.tr/v4/UIWebService/Search.aspx',
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
		'vakifbank-cp' => [
			'name' => 'Vakıfbank (PayFlex Common Payment)',
			'class' => SinyorPos\Gateways\PayFlexCPV4Pos::class,
			'urls' => [
				'production' => 'https://cpweb.vakifbank.com.tr/CommonPayment/api/RegisterTransaction',
				'test' => 'https://cptest.vakifbank.com.tr/CommonPayment/api/RegisterTransaction',
				'gateway' => [
					'production' => 'https://cpweb.vakifbank.com.tr/CommonPayment/SecurePayment',
					'test' => 'https://cptest.vakifbank.com.tr/CommonPayment/SecurePayment',
				],
				'query' => [
					'production' => 'https://cpweb.vakifbank.com.tr/CommonPayment/api/VposTransaction',
					'test' => 'https://cptest.vakifbank.com.tr/CommonPayment/api/VposTransaction',
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
	    'denizbank' => [
		    'name' => 'Denizbank',
		    'class' => SinyorPos\Gateways\InterPos::class,
		    'urls' => [
			    'production' => 'https://inter-vpos.com.tr/mpi/Default.aspx',
			    'test' => 'https://test.inter-vpos.com.tr/mpi/Default.aspx',
			    'gateway' => [
				    'production' => 'https://inter-vpos.com.tr/mpi/Default.aspx',
				    'test' => 'https://test.inter-vpos.com.tr/mpi/Default.aspx',
			    ],
			    'gateway_3d_host' => [
				    'production' => 'https://inter-vpos.com.tr/mpi/3DHost.aspx',
				    'test' => 'https://test.inter-vpos.com.tr/mpi/3DHost.aspx',
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
	    'kuveytpos' => [
		    'name' => 'Kuveyt Türk',
		    'class' => SinyorPos\Gateways\KuveytPos::class,
		    'urls' => [
			    'production' => 'https://boa.kuveytturk.com.tr/sanalposservice/Home/ThreeDModelProvisionGate',
			    'test' => 'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home/ThreeDModelProvisionGate',
			    'gateway' => [
				    'production' => 'https://boa.kuveytturk.com.tr/sanalposservice/Home/ThreeDModelPayGate',
				    'test' => 'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home/ThreeDModelPayGate',
			    ],
			    'query'       => [
				    'production' => 'https://boa.kuveytturk.com.tr/BOA.Integration.WCFService/BOA.Integration.VirtualPos/VirtualPosService.svc?wsdl',
				    'test' => 'https://boatest.kuveytturk.com.tr/BOA.Integration.WCFService/BOA.Integration.VirtualPos/VirtualPosService.svc?wsdl',
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
	    ]
    ],
];
