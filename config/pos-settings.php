<?php

return [
    'currencies' => [ // Para birimleri
        'TRY' => 949,
        'EUR' => 978,
        'USD' => 840,
    ],
    'banks' => [ // Bankalar
	    'akbank' => [
		    'name' => 'Akbank',
		    'class' => EceoPos\Gateways\ESTVirtualPos::class,
		    'urls' => [
			    'production' => 'https://www.sanalakpos.com/fim/api',
			    'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
			    'gateway' => [
				    'production' => 'https://www.sanalakpos.com/fim/est3Dgate',
				    'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
			    ],
			    'gateway_3d_host' => [
				    'production' => 'https://sanalpos.isbank.com.tr/fim/est3Dgate',
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
	    'ziraat' => [
		    'name' => 'Ziraat Bankası',
		    'class' => EceoPos\Gateways\ESTVirtualPos::class,
		    'urls' => [
			    'production' => 'https://sanalpos2.ziraatbank.com.tr/fim/api',
			    'test' => 'https://entegrasyon.asseco-see.com.tr/fim/api',
			    'gateway' => [
				    'production' => 'https://sanalpos2.ziraatbank.com.tr/fim/est3dgate',
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
	    'finansbank' => [
		    'name' => 'QNB Finansbank',
		    'class' => EceoPos\Gateways\ESTVirtualPos::class,
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
		    'class' => EceoPos\Gateways\ESTVirtualPos::class,
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
		    'name' => 'İşbank',
		    'class' => EceoPos\Gateways\ESTVirtualPos::class,
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
	    'yapikredi' => [
		    'name' => 'Yapıkredi',
		    'class' => EceoPos\Gateways\PosNetVirtualPos::class,
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
	    'garanti' => [
		    'name' => 'Garanti',
		    'class' => EceoPos\Gateways\GarantiVirtualPos::class,
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
		    'name' => 'QNB Finansbank PayFor',
		    'class' => EceoPos\Gateways\PayForVirtualPos::class,
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
		    'name' => 'Vakıfbank',
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
	    'denizbank' => [
		    'name' => 'Denizbank',
		    'class' => EceoPos\Gateways\InterVirtualPos::class,
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
		    'class' => EceoPos\Gateways\KuveytVirtualPos::class,
		    'urls' => [
			    'production' => 'https://boa.kuveytturk.com.tr/sanalposservice/Home/ThreeDModelProvisionGate',
			    'test' => 'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home/ThreeDModelProvisionGate',
			    'gateway' => [
				    'production' => 'https://boa.kuveytturk.com.tr/sanalposservice/Home/ThreeDModelPayGate',
				    'test' => 'https://boatest.kuveytturk.com.tr/boa.virtualpos.services/Home/ThreeDModelPayGate',
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
