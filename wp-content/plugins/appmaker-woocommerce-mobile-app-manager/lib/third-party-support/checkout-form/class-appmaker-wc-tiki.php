<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_tiki {

	public static function className() {
		return 'APPMAKER_WC_tiki';
	}

	/**
	 * APPMAKER_WC_tiki constructor.
	 */
	public function __construct() {
		add_filter( 'appmaker_wc_checkout_fields', array( self::className(), 'indonesia_field' ), 10, 2 );

		add_filter( 'appmaker_wc_dependency_billing_kota', array( self::className(), 'kota_dependency' ), 10, 2 );
		add_filter( 'appmaker_wc_dependency_shipping_kota', array( self::className(), 'kota_dependency' ), 10, 2 );

		add_filter( 'appmaker_wc_dependency_billing_city', array( self::className(), 'city_dependency' ), 10, 2 );
		add_filter( 'appmaker_wc_dependency_shipping_city', array( self::className(), 'city_dependency' ), 10, 2 );

		add_filter( 'appmaker_wc_before_shipping_methods', array( self::className(), 'tiki_shipping_fix' ) );
		add_action( 'appmaker_wc_before_checkout', array( self::className(), 'tiki_shipping_fix' ) );

	}

	public static function tiki_shipping_fix( $request ) {
		if ( ! empty( $request['billing_city'] ) && ! empty( $request['billing_kota'] ) ) {
			$request['billing_city'] = $_POST['billing_city'] = $request['billing_city'] . ', ' . $request['billing_kota'];
		}

		if ( ! empty( $request['shipping_city'] ) && ! empty( $request['shipping_kota'] ) ) {
			$request['shipping_city'] = $_POST['shipping_city'] = $request['shipping_city'] . ', ' . $request['shipping_kota'];
		}

		return $request;
	}

	public static function kota_dependency( $dependency, $key ) {
		if ( 'billing_kota' === $key ) {
			$dependency = array( 'on' => 'billing_state' );
		} elseif ( 'shipping_kota' === $key ) {
			$dependency = array( 'on' => 'shipping_state' );
		}

		return $dependency;
	}

	public static function city_dependency( $dependency, $key ) {
		if ( 'billing_city' === $key ) {
			$dependency = array( 'on' => 'billing_kota' );
		} elseif ( 'shipping_city' === $key ) {
			$dependency = array( 'on' => 'shipping_kota' );
		}

		return $dependency;
	}


	/**
	 * @param $fields
	 * @param $section
	 *
	 * @return array|mixed
	 * @internal param array $args
	 *
	 */
	public static function indonesia_field( $fields, $section ) {
		if ( 'billing' === $section || 'shipping' === $section ) {
			$fields[ $section . '_kota' ]['type']      = 'dependent-select';
			$fields[ $section . '_kota' ]['options']   = self::static_shipping_get_kota();
			$fields[ $section . '_kota' ]['dependent'] = true;

			$fields[ $section . '_city' ]['type']      = 'dependent-select';
			$fields[ $section . '_city' ]['options']   = self::static_shipping_get_kecamatan();
			$fields[ $section . '_city' ]['dependent'] = true;
		}

		return $fields;
	}

	public static function static_shipping_get_kota() {
		return json_decode('{
                    "AC": {
                        "items": {
                            "Kota Banda Aceh": "Kota Banda Aceh",
                            "Kota Langsa": "Kota Langsa",
                            "Kota Lhokseumawe": "Kota Lhokseumawe",
                            "Kab. Aceh Besar": "Kab. Aceh Besar",
                            "Kab. Bireuen": "Kab. Bireuen",
                            "Kab. Aceh Barat": "Kab. Aceh Barat",
                            "Kab. Pidie": "Kab. Pidie",
                            "Kab. Aceh Tengah": "Kab. Aceh Tengah",
                            "Kab. Aceh Selatan": "Kab. Aceh Selatan",
                            "Kota Sabang": "Kota Sabang",
                            "Kab. Aceh Barat Daya": "Kab. Aceh Barat Daya",
                            "Kab. Aceh Jaya": "Kab. Aceh Jaya",
                            "Kab. Aceh Singkil": "Kab. Aceh Singkil",
                            "Kab. Aceh Tamiang": "Kab. Aceh Tamiang",
                            "Kab. Aceh Timur": "Kab. Aceh Timur",
                            "Kab. Aceh Utara": "Kab. Aceh Utara",
                            "Kab. Bener Meriah": "Kab. Bener Meriah",
                            "Kab. Nagan Raya": "Kab. Nagan Raya",
                            "Kab. Pidie Jaya": "Kab. Pidie Jaya",
                            "Kab. Simeulue": "Kab. Simeulue",
                            "Kota Subulussalam": "Kota Subulussalam"
                        }
                    },
                    "SU": {
                        "items": {
                            "Kota Medan": "Kota Medan",
                            "Kab. Asahan": "Kab. Asahan",
                            "Kab. Tapanuli Tengah": "Kab. Tapanuli Tengah",
                            "Kota Sibolga": "Kota Sibolga",
                            "Kab. Dairi": "Kab. Dairi",
                            "Kab. Deli Serdang": "Kab. Deli Serdang",
                            "Kab. Toba Samosir": "Kab. Toba Samosir",
                            "Kota Binjai": "Kota Binjai",
                            "Kab. Nias": "Kab. Nias",
                            "Kab. Simalungun": "Kab. Simalungun",
                            "Kab. Labuhanbatu": "Kab. Labuhanbatu",
                            "Kab. Karo": "Kab. Karo",
                            "Kab. Langkat": "Kab. Langkat",
                            "Kab. Tapanuli Utara": "Kab. Tapanuli Utara",
                            "Kota Tebing Tinggi": "Kota Tebing Tinggi",
                            "Kab. Batubara": "Kab. Batubara",
                            "Kab. Humbang Hasundutan": "Kab. Humbang Hasundutan",
                            "Kab. Samosir": "Kab. Samosir",
                            "Kab. Serdang Bedagai": "Kab. Serdang Bedagai",
                            "Kota Tanjungbalai": "Kota Tanjungbalai",
                            "Kota Pematangsiantar": "Kota Pematangsiantar"
                        }
                    },
                    "SB": {
                        "items": {
                            "Kota Padangsidempuan": "Kota Padangsidempuan",
                            "Kota Padang": "Kota Padang",
                            "Kab. Tanah Datar": "Kab. Tanah Datar",
                            "Kota Bukittinggi": "Kota Bukittinggi",
                            "Kab. Pasaman": "Kab. Pasaman",
                            "Kab. Pesisir Selatan": "Kab. Pesisir Selatan",
                            "Kab. Padang Pariaman": "Kab. Padang Pariaman",
                            "Kota Pariaman": "Kota Pariaman",
                            "Kota Payakumbuh": "Kota Payakumbuh",
                            "Kota Sawahlunto": "Kota Sawahlunto",
                            "Kota Solok": "Kota Solok",
                            "Kota Padangpanjang": "Kota Padangpanjang",
                            "Kab. Dharmasraya": "Kab. Dharmasraya",
                            "Kab. Lima Puluh Kota": "Kab. Lima Puluh Kota",
                            "Kab. Kepulauan Mentawai": "Kab. Kepulauan Mentawai",
                            "Kab. Pasaman Barat": "Kab. Pasaman Barat",
                            "Kab. Solok": "Kab. Solok",
                            "Kab. Solok Selatan": "Kab. Solok Selatan",
                            "Kab. Sijunjung": "Kab. Sijunjung",
                            "Kab. Agam": "Kab. Agam"
                        }
                    },
                    "RI": {
                        "items": {
                            "Kota Pekanbaru": "Kota Pekanbaru",
                            "Kota Dumai": "Kota Dumai",
                            "Kab. Kampar": "Kab. Kampar",
                            "Kab. Bengkalis": "Kab. Bengkalis",
                            "Kab. Indragiri Hulu": "Kab. Indragiri Hulu",
                            "Kab. Indragiri Hilir": "Kab. Indragiri Hilir",
                            "Kab. Rokan Hilir": "Kab. Rokan Hilir",
                            "Kab. Kuantan Singingi": "Kab. Kuantan Singingi",
                            "Kab. Pelalawan": "Kab. Pelalawan",
                            "Kab. Rokan Hulu": "Kab. Rokan Hulu",
                            "Kab. Siak": "Kab. Siak",
                            "Kab. Kepulauan Meranti": "Kab. Kepulauan Meranti"
                        }
                    },
                    "KR": {
                        "items": {
                            "Kota Batam": "Kota Batam",
                            "Kab. Lingga": "Kab. Lingga",
                            "Kab. Karimun": "Kab. Karimun",
                            "Kota Tanjung Pinang": "Kota Tanjung Pinang",
                            "Kab. Bintan": "Kab. Bintan"
                        }
                    },
                    "JA": {
                        "items": {
                            "Kota Jambi": "Kota Jambi",
                            "Kab. Merangin": "Kab. Merangin",
                            "Kab. Batanghari": "Kab. Batanghari",
                            "Kab. Bungo": "Kab. Bungo",
                            "Kab. Kerinci": "Kab. Kerinci",
                            "Kab. Muaro Jambi": "Kab. Muaro Jambi",
                            "Kab. Sarolangun": "Kab. Sarolangun",
                            "Kab. Tanjung Jabung Timur": "Kab. Tanjung Jabung Timur",
                            "Kab. Tebo": "Kab. Tebo"
                        }
                    },
                    "SS": {
                        "items": {
                            "Kota Palembang": "Kota Palembang",
                            "Kab. Musi Rawas": "Kab. Musi Rawas",
                            "Kab. Ogan Komering Ulu": "Kab. Ogan Komering Ulu",
                            "Kab. Ogan Komering Ilir": "Kab. Ogan Komering Ilir",
                            "Kab. Lahat": "Kab. Lahat",
                            "Kab. Muara Enim": "Kab. Muara Enim",
                            "Kota Pagar Alam": "Kota Pagar Alam",
                            "Kota Prabumulih": "Kota Prabumulih",
                            "Kab. Musi Banyuasin": "Kab. Musi Banyuasin",
                            "Kab. Banyuasin": "Kab. Banyuasin",
                            "Kab. Empat Lawang": "Kab. Empat Lawang",
                            "Kab. Ogan Ilir": "Kab. Ogan Ilir",
                            "Kab. Ogan Komering Ulu Timur": "Kab. Ogan Komering Ulu Timur",
                            "Kab. Ogan Komering Ulu Selatan": "Kab. Ogan Komering Ulu Selatan",
                            "Kota Lubuklinggau": "Kota Lubuklinggau"
                        }
                    },
                    "BB": {
                        "items": {
                            "Kota Pangkal Pinang": "Kota Pangkal Pinang",
                            "Kab. Bangka": "Kab. Bangka",
                            "Kab. Bangka Barat": "Kab. Bangka Barat",
                            "Kab. Bangka Tengah": "Kab. Bangka Tengah",
                            "Kab. Bangka Selatan": "Kab. Bangka Selatan",
                            "Kab. Belitung": "Kab. Belitung",
                            "Kab. Belitung Timur": "Kab. Belitung Timur"
                        }
                    },
                    "BE": {
                        "items": {
                            "Kota Bengkulu": "Kota Bengkulu",
                            "Kab. Bengkulu Utara": "Kab. Bengkulu Utara",
                            "Kab. Rejang Lebong": "Kab. Rejang Lebong",
                            "Kab. Bengkulu Selatan": "Kab. Bengkulu Selatan",
                            "Kab. Kaur": "Kab. Kaur",
                            "Kab. Kepahiang": "Kab. Kepahiang",
                            "Kab. Lebong": "Kab. Lebong",
                            "Kab. Mukomuko": "Kab. Mukomuko",
                            "Kab. Seluma": "Kab. Seluma"
                        }
                    },
                    "LA": {
                        "items": {
                            "Kota Bandar Lampung": "Kota Bandar Lampung",
                            "Kab. Lampung Selatan": "Kab. Lampung Selatan",
                            "Kab. Lampung Utara": "Kab. Lampung Utara",
                            "Kota Metro": "Kota Metro",
                            "Kab. Lampung Barat": "Kab. Lampung Barat",
                            "Kab. Lampung Tengah": "Kab. Lampung Tengah",
                            "Kab. Lampung Timur": "Kab. Lampung Timur",
                            "Kab. Tulang Bawang": "Kab. Tulang Bawang",
                            "Kab. Tanggamus": "Kab. Tanggamus",
                            "Kab. Pesawaran": "Kab. Pesawaran"
                        }
                    },
                    "JK": {
                        "items": {
                            "DKI Jakarta": "DKI Jakarta",
                            "Kota Administrasi Jakarta Barat": "Kota Administrasi Jakarta Barat",
                            "Kota Administrasi Jakarta Selatan": "Kota Administrasi Jakarta Selatan",
                            "Kota Administrasi Jakarta Pusat": "Kota Administrasi Jakarta Pusat",
                            "Kota Administrasi Jakarta Utara": "Kota Administrasi Jakarta Utara",
                            "Kota Administrasi Jakarta Timur": "Kota Administrasi Jakarta Timur"
                        }
                    },
                    "JB": {
                        "items": {
                            "Kota Bandung": "Kota Bandung",
                            "Kab. Bandung": "Kab. Bandung",
                            "Kota Cimahi": "Kota Cimahi",
                            "Kab. Sumedang": "Kab. Sumedang",
                            "Kota Tasikmalaya": "Kota Tasikmalaya",
                            "Kota Banjar": "Kota Banjar",
                            "Kab. Ciamis": "Kab. Ciamis",
                            "Kab. Tasikmalaya": "Kab. Tasikmalaya",
                            "Kab. Garut": "Kab. Garut",
                            "Kab. Purwakarta": "Kab. Purwakarta",
                            "Kab. Bandung Barat": "Kab. Bandung Barat",
                            "Kab. Subang": "Kab. Subang",
                            "Kota Bekasi": "Kota Bekasi",
                            "Kota Bogor": "Kota Bogor",
                            "Kab. Bogor": "Kab. Bogor",
                            "Kota Cirebon": "Kota Cirebon",
                            "Kab. Indramayu": "Kab. Indramayu",
                            "Kab. Kuningan": "Kab. Kuningan",
                            "Kab. Majalengka": "Kab. Majalengka",
                            "Kab. Cirebon": "Kab. Cirebon",
                            "Kota Depok": "Kota Depok",
                            "Kab. Karawang": "Kab. Karawang",
                            "Kab. Sukabumi": "Kab. Sukabumi",
                            "Kota Sukabumi": "Kota Sukabumi",
                            "Kab. Cianjur": "Kab. Cianjur"
                        }
                    },
                    "BT": {
                        "items": {
                            "Kota Cilegon": "Kota Cilegon",
                            "Kab. Pandeglang": "Kab. Pandeglang",
                            "Kab. Lebak": "Kab. Lebak",
                            "Kota Serang": "Kota Serang",
                            "Kab. Serang": "Kab. Serang",
                            "Kota Tangerang": "Kota Tangerang",
                            "Kab. Tangerang": "Kab. Tangerang",
                            "Kota Tangerang Selatan": "Kota Tangerang Selatan"
                        }
                    },
                    "JT": {
                        "items": {
                            "Kab. Cilacap": "Kab. Cilacap",
                            "Kota Magelang": "Kota Magelang",
                            "Kab. Kebumen": "Kab. Kebumen",
                            "Kab. Wonosobo": "Kab. Wonosobo",
                            "Kab. Purworejo": "Kab. Purworejo",
                            "Kab. Temanggung": "Kab. Temanggung",
                            "Kab. Magelang": "Kab. Magelang",
                            "Kota Surakarta": "Kota Surakarta",
                            "Kab. Boyolali": "Kab. Boyolali",
                            "Kab. Karanganyar": "Kab. Karanganyar",
                            "Kab. Klaten": "Kab. Klaten",
                            "Kab. Sragen": "Kab. Sragen",
                            "Kab. Sukoharjo": "Kab. Sukoharjo",
                            "Kab. Wonogiri": "Kab. Wonogiri",
                            "Kota Semarang": "Kota Semarang",
                            "Kab. Jepara": "Kab. Jepara",
                            "Kab. Kudus": "Kab. Kudus",
                            "Kota Pekalongan": "Kota Pekalongan",
                            "Kab. Banyumas": "Kab. Banyumas",
                            "Kab. Batang": "Kab. Batang",
                            "Kab. Blora": "Kab. Blora",
                            "Kab. Brebes": "Kab. Brebes",
                            "Kab. Demak": "Kab. Demak",
                            "Kab. Kendal": "Kab. Kendal",
                            "Kab. Pati": "Kab. Pati",
                            "Kab. Pemalang": "Kab. Pemalang",
                            "Kab. Grobogan": "Kab. Grobogan",
                            "Kab. Rembang": "Kab. Rembang",
                            "Kota Salatiga": "Kota Salatiga",
                            "Kab. Tegal": "Kab. Tegal",
                            "Kota Tegal": "Kota Tegal",
                            "Kab. Semarang": "Kab. Semarang",
                            "Kab. Purbalingga": "Kab. Purbalingga",
                            "Kab. Banjarnegara": "Kab. Banjarnegara",
                            "Kab. Pekalongan": "Kab. Pekalongan"
                        }
                    },
                    "JI": {
                        "items": {
                            "Kab. Jember": "Kab. Jember",
                            "Kab. Banyuwangi": "Kab. Banyuwangi",
                            "Kab. Bondowoso": "Kab. Bondowoso",
                            "Kota Kediri": "Kota Kediri",
                            "Kab. Kediri": "Kab. Kediri",
                            "Kota Madiun": "Kota Madiun",
                            "Kab. Madiun": "Kab. Madiun",
                            "Kab. Magetan": "Kab. Magetan",
                            "Kab. Ngawi": "Kab. Ngawi",
                            "Kab. Pacitan": "Kab. Pacitan",
                            "Kab. Ponorogo": "Kab. Ponorogo",
                            "Kab. Mojokerto": "Kab. Mojokerto",
                            "Kab. Sidoarjo": "Kab. Sidoarjo",
                            "Kota Mojokerto": "Kota Mojokerto",
                            "Kota Malang": "Kota Malang",
                            "Kab. Blitar": "Kab. Blitar",
                            "Kota Blitar": "Kota Blitar",
                            "Kota Batu": "Kota Batu",
                            "Kab. Malang": "Kab. Malang",
                            "Kab. Probolinggo": "Kab. Probolinggo",
                            "Kota Probolinggo": "Kota Probolinggo",
                            "Kab. Lumajang": "Kab. Lumajang",
                            "Kab. Situbondo": "Kab. Situbondo",
                            "Kab. Pasuruan": "Kab. Pasuruan",
                            "Kota Pasuruan": "Kota Pasuruan",
                            "Kab. Bojonegoro": "Kab. Bojonegoro",
                            "Kota Surabaya": "Kota Surabaya",
                            "Kab. Gresik": "Kab. Gresik",
                            "Kab. Lamongan": "Kab. Lamongan",
                            "Kab. Bangkalan": "Kab. Bangkalan",
                            "Kab. Jombang": "Kab. Jombang",
                            "Kab. Nganjuk": "Kab. Nganjuk",
                            "Kab. Pamekasan": "Kab. Pamekasan",
                            "Kab. Sampang": "Kab. Sampang",
                            "Kab. Sumenep": "Kab. Sumenep",
                            "Kab. Tuban": "Kab. Tuban",
                            "Kab. Tulungagung": "Kab. Tulungagung",
                            "Kab. Trenggalek": "Kab. Trenggalek"
                        }
                    },
                    "YO": {
                        "items": {
                            "Kota Yogyakarta": "Kota Yogyakarta",
                            "Kab. Bantul": "Kab. Bantul",
                            "Kab. Sleman": "Kab. Sleman",
                            "Kab. Kulon Progo": "Kab. Kulon Progo",
                            "Kab. Gunungkidul": "Kab. Gunungkidul"
                        }
                    },
                    "BA": {
                        "items": {
                            "Kota Denpasar": "Kota Denpasar",
                            "Kab. Karangasem": "Kab. Karangasem",
                            "Kab. Bangli": "Kab. Bangli",
                            "Kab. Gianyar": "Kab. Gianyar",
                            "Kab. Jembrana": "Kab. Jembrana",
                            "Kab. Klungkung": "Kab. Klungkung",
                            "Kab. Buleleng": "Kab. Buleleng",
                            "Kab. Tabanan": "Kab. Tabanan",
                            "Kab. Badung": "Kab. Badung"
                        }
                    },
                    "NB": {
                        "items": {
                            "Kota Mataram": "Kota Mataram",
                            "Kota Bima": "Kota Bima",
                            "Kab. Dompu": "Kab. Dompu",
                            "Kab. Lombok Tengah": "Kab. Lombok Tengah",
                            "Kab. Lombok Timur": "Kab. Lombok Timur",
                            "Kab. Sumbawa": "Kab. Sumbawa",
                            "Kab. Lombok Barat": "Kab. Lombok Barat",
                            "Kab. Lombok Utara": "Kab. Lombok Utara",
                            "Kab. Sumbawa Barat": "Kab. Sumbawa Barat",
                            "Kab. Bima": "Kab. Bima"
                        }
                    },
                    "NT": {
                        "items": {
                            "Kota Kupang": "Kota Kupang",
                            "Kab. Belu": "Kab. Belu",
                            "Kab. Alor": "Kab. Alor",
                            "Kab. Timor Tengah Utara": "Kab. Timor Tengah Utara",
                            "Kab. Flores Timur": "Kab. Flores Timur",
                            "Kab. Sikka": "Kab. Sikka",
                            "Kab. Manggarai": "Kab. Manggarai",
                            "Kab. Timor Tengah Selatan": "Kab. Timor Tengah Selatan",
                            "Kab. Ende": "Kab. Ende",
                            "Kab. Ngada": "Kab. Ngada",
                            "Kab. Sumba Timur": "Kab. Sumba Timur",
                            "Kab. Sumba Barat": "Kab. Sumba Barat",
                            "Kab. Manggarai Barat": "Kab. Manggarai Barat",
                            "Kab. Rote Ndao": "Kab. Rote Ndao",
                            "Kab. Sumba Barat Daya": "Kab. Sumba Barat Daya",
                            "Kab. Lembata": "Kab. Lembata"
                        }
                    },
                    "KB": {
                        "items": {
                            "Kota Pontianak": "Kota Pontianak",
                            "Kab. Ketapang": "Kab. Ketapang",
                            "Kab. Sanggau": "Kab. Sanggau",
                            "Kota Singkawang": "Kota Singkawang",
                            "Kab. Sintang": "Kab. Sintang",
                            "Kab. Kapuas Hulu": "Kab. Kapuas Hulu",
                            "Kab. Bengkayang": "Kab. Bengkayang",
                            "Kab. Landak": "Kab. Landak",
                            "Kab. Melawi": "Kab. Melawi",
                            "Kab. Mempawah": "Kab. Mempawah",
                            "Kab. Sambas": "Kab. Sambas",
                            "Kab. Kubu Raya": "Kab. Kubu Raya"
                        }
                    },
                    "KT": {
                        "items": {
                            "Kab. Barito Utara": "Kab. Barito Utara",
                            "Kab. Barito Timur": "Kab. Barito Timur",
                            "Kab. Murung Raya": "Kab. Murung Raya",
                            "Kab. Barito Selatan": "Kab. Barito Selatan",
                            "Kota Palangka Raya": "Kota Palangka Raya",
                            "Kab. Katingan": "Kab. Katingan",
                            "Kab. Kapuas": "Kab. Kapuas",
                            "Kab. Gunung Mas": "Kab. Gunung Mas",
                            "Kab. Kotawaringin Barat": "Kab. Kotawaringin Barat",
                            "Kab. Kotawaringin Timur": "Kab. Kotawaringin Timur",
                            "Kab. Pulang Pisau": "Kab. Pulang Pisau"
                        }
                    },
                    "KI": {
                        "items": {
                            "Kota Balikpapan": "Kota Balikpapan",
                            "Kab. Paser": "Kab. Paser",
                            "Kab. Penajam Paser Utara": "Kab. Penajam Paser Utara",
                            "Kota Bontang": "Kota Bontang",
                            "Kab. Kutai Timur": "Kab. Kutai Timur",
                            "Kota Samarinda": "Kota Samarinda",
                            "Kab. Kutai Kartanegara": "Kab. Kutai Kartanegara",
                            "Kab. Kutai Barat": "Kab. Kutai Barat"
                        }
                    },
                    "KS": {
                        "items": {
                            "Kota Banjarmasin": "Kota Banjarmasin",
                            "Kab. Hulu Sungai Tengah": "Kab. Hulu Sungai Tengah",
                            "Kab. Hulu Sungai Selatan": "Kab. Hulu Sungai Selatan",
                            "Kab. Kotabaru": "Kab. Kotabaru",
                            "Kab. Tabalong": "Kab. Tabalong",
                            "Kota Banjarbaru": "Kota Banjarbaru",
                            "Kab. Hulu Sungai Utara": "Kab. Hulu Sungai Utara",
                            "Kab. Barito Kuala": "Kab. Barito Kuala",
                            "Kab. Banjar": "Kab. Banjar",
                            "Kab. Tanah Laut": "Kab. Tanah Laut",
                            "Kab. Tapin": "Kab. Tapin",
                            "Kab. Balangan": "Kab. Balangan",
                            "Kab. Tanah Bumbu": "Kab. Tanah Bumbu"
                        }
                    },
                    "KU": {
                        "items": {
                            "Kota Tarakan": "Kota Tarakan",
                            "Kab. Bulungan": "Kab. Bulungan",
                            "Kab. Malinau": "Kab. Malinau",
                            "Kab. Nunukan": "Kab. Nunukan",
                            "Kab. Tana Tidung": "Kab. Tana Tidung"
                        }
                    },
                    "SA": {
                        "items": {
                            "Kota Manado": "Kota Manado",
                            "Kab. Bolaang Mongondow": "Kab. Bolaang Mongondow",
                            "Kab. Kepulauan Sangihe": "Kab. Kepulauan Sangihe",
                            "Kab. Minahasa": "Kab. Minahasa",
                            "Kota Bitung": "Kota Bitung",
                            "Kab. Kepulauan Talaud": "Kab. Kepulauan Talaud",
                            "Kab. Minahasa Utara": "Kab. Minahasa Utara",
                            "Kab. Minahasa Selatan": "Kab. Minahasa Selatan",
                            "Kab. Minahasa Tenggara": "Kab. Minahasa Tenggara",
                            "Kota Tomohon": "Kota Tomohon"
                        }
                    },
                    "ST": {
                        "items": {
                            "Kota Palu": "Kota Palu",
                            "Kab. Banggai": "Kab. Banggai",
                            "Kab. Poso": "Kab. Poso",
                            "Kab. Toli-Toli": "Kab. Toli-Toli",
                            "Kab. Buol": "Kab. Buol",
                            "Kab. Donggala": "Kab. Donggala",
                            "Kab. Morowali": "Kab. Morowali",
                            "Kab. Parigi Moutong": "Kab. Parigi Moutong",
                            "Kab. Tojo Una-Una": "Kab. Tojo Una-Una"
                        }
                    },
                    "SG": {
                        "items": {
                            "Kota Kendari": "Kota Kendari",
                            "Kota Bau-Bau": "Kota Bau-Bau",
                            "Kab. Kolaka": "Kab. Kolaka",
                            "Kab. Muna": "Kab. Muna",
                            "Kab. Konawe": "Kab. Konawe",
                            "Kab. Bombana": "Kab. Bombana",
                            "Kab. Buton Utara": "Kab. Buton Utara",
                            "Kab. Kolaka Utara": "Kab. Kolaka Utara",
                            "Kab. Wakatobi": "Kab. Wakatobi"
                        }
                    },
                    "SR": {
                        "items": {
                            "Kab. Mamuju": "Kab. Mamuju",
                            "Kab. Majene": "Kab. Majene",
                            "Kab. Polewali Mandar": "Kab. Polewali Mandar",
                            "Kab. Mamasa": "Kab. Mamasa",
                            "Kab. Mamuju Utara": "Kab. Mamuju Utara"
                        }
                    },
                    "SN": {
                        "items": {
                            "Kota Makassar": "Kota Makassar",
                            "Kab. Bantaeng": "Kab. Bantaeng",
                            "Kab. Barru": "Kab. Barru",
                            "Kab. Bulukumba": "Kab. Bulukumba",
                            "Kab. Enrekang": "Kab. Enrekang",
                            "Kab. Jeneponto": "Kab. Jeneponto",
                            "Kab. Tana Toraja": "Kab. Tana Toraja",
                            "Kab. Toraja Utara": "Kab. Toraja Utara",
                            "Kab. Maros": "Kab. Maros",
                            "Kota Palopo": "Kota Palopo",
                            "Kab. Pinrang": "Kab. Pinrang",
                            "Kab. Sidenreng Rappang": "Kab. Sidenreng Rappang",
                            "Kab. Wajo": "Kab. Wajo",
                            "Kab. Sinjai": "Kab. Sinjai",
                            "Kab. Gowa": "Kab. Gowa",
                            "Kab. Takalar": "Kab. Takalar",
                            "Kab. Bone": "Kab. Bone",
                            "Kab. Soppeng": "Kab. Soppeng",
                            "Kab. Kepulauan Selayar": "Kab. Kepulauan Selayar",
                            "Kab. Pangkajene dan Kepulauan": "Kab. Pangkajene dan Kepulauan",
                            "Kota Parepare": "Kota Parepare",
                            "Kab. Luwu Utara": "Kab. Luwu Utara",
                            "Kab. Luwu Timur": "Kab. Luwu Timur"
                        }
                    },
                    "GO": {
                        "items": {
                            "Kota Gorontalo": "Kota Gorontalo",
                            "Kab. Gorontalo": "Kab. Gorontalo",
                            "Kab. Boalemo": "Kab. Boalemo",
                            "Kab. Bone Bolango": "Kab. Bone Bolango",
                            "Kab. Gorontalo Utara": "Kab. Gorontalo Utara",
                            "Kab. Pohuwato": "Kab. Pohuwato"
                        }
                    },
                    "MA": {
                        "items": {
                            "Kota Ambon": "Kota Ambon",
                            "Kab. Maluku Tengah": "Kab. Maluku Tengah",
                            "Kab. Maluku Tenggara": "Kab. Maluku Tenggara",
                            "Kab. Buru": "Kab. Buru",
                            "Kab. Seram Bagian Barat": "Kab. Seram Bagian Barat",
                            "Kab. Maluku Tenggara Barat": "Kab. Maluku Tenggara Barat"
                        }
                    },
                    "MU": {
                        "items": {
                            "Kota Ternate": "Kota Ternate",
                            "Kab. Halmahera Utara": "Kab. Halmahera Utara",
                            "Kota Tidore Kepulauan": "Kota Tidore Kepulauan"
                        }
                    },
                    "PA": {
                        "items": {
                            "Kota Jayapura": "Kota Jayapura",
                            "Kab. Jayapura": "Kab. Jayapura",
                            "Kab. Biak Numfor": "Kab. Biak Numfor",
                            "Kab. Merauke": "Kab. Merauke",
                            "Kab. Jayawijaya": "Kab. Jayawijaya",
                            "Kab. Nabire": "Kab. Nabire",
                            "Kab. Paniai": "Kab. Paniai",
                            "Kab. Puncak Jaya": "Kab. Puncak Jaya",
                            "Kab. Intan Jaya": "Kab. Intan Jaya",
                            "Kab. Deiyai": "Kab. Deiyai",
                            "Kab. Mimika": "Kab. Mimika"
                        }
                    },
                    "PB": {
                        "items": {
                            "Kab. Sorong": "Kab. Sorong",
                            "Kota Sorong": "Kota Sorong",
                            "Kab. Fakfak": "Kab. Fakfak",
                            "Kab. Kaimana": "Kab. Kaimana",
                            "Kab. Raja Ampat": "Kab. Raja Ampat",
                            "Kab. Sorong Selatan": "Kab. Sorong Selatan",
                            "Kab. Teluk Bintuni": "Kab. Teluk Bintuni",
                            "Kab. Manokwari": "Kab. Manokwari"
                        }
                    }
                }');
	}

	public static function static_shipping_get_kecamatan() {
		return json_decode('{
                    "Kota Banda Aceh": {
                        "items": {
                            "Banda Aceh": "Banda Aceh",
                            "Meuraksa": "Meuraksa",
                            "Jaya Baru": "Jaya Baru",
                            "Banda Raya": "Banda Raya",
                            "Baiturrahman": "Baiturrahman",
                            "Lueng Bata": "Lueng Bata",
                            "Kuta Alam": "Kuta Alam",
                            "Kuta Raja": "Kuta Raja",
                            "Syiah Kuala": "Syiah Kuala",
                            "Ulee Kareng": "Ulee Kareng"
                        }
                    },
                    "Kota Langsa": {
                        "items": {
                            "Langsa": "Langsa",
                            "Langsa Barat": "Langsa Barat",
                            "Langsa Kota": "Langsa Kota",
                            "Langsa Lama": "Langsa Lama",
                            "Langsa Timur": "Langsa Timur"
                        }
                    },
                    "Kota Lhokseumawe": {
                        "items": {
                            "Lhokseumawe": "Lhokseumawe",
                            "Muara Dua": "Muara Dua",
                            "Banda Sakti": "Banda Sakti",
                            "Blang Mangat": "Blang Mangat"
                        }
                    },
                    "Kab. Aceh Besar": {
                        "items": {
                            "Janto": "Janto",
                            "Lembah Seulawah": "Lembah Seulawah"
                        }
                    },
                    "Kab. Bireuen": {
                        "items": {
                            "Bireuen": "Bireuen",
                            "Jeunieb": "Jeunieb",
                            "Peusangan": "Peusangan",
                            "Peudada": "Peudada",
                            "Samalanga": "Samalanga",
                            "Ganda Pura": "Ganda Pura",
                            "Makmur": "Makmur",
                            "Jangka": "Jangka",
                            "Juli": "Juli",
                            "Kota Juang": "Kota Juang",
                            "Kuta Blang": "Kuta Blang",
                            "Pandrah": "Pandrah",
                            "Peusangan Selatan": "Peusangan Selatan",
                            "Peusangan Siblah Krueng": "Peusangan Siblah Krueng",
                            "Primbang": "Primbang",
                            "Simpang Mamplam": "Simpang Mamplam"
                        }
                    },
                    "Kab. Aceh Barat": {
                        "items": {
                            "Meulaboh": "Meulaboh",
                            "Kec. Kaway XVI": "Kec. Kaway XVI",
                            "Arongan Lambalek": "Arongan Lambalek",
                            "Johan Pahlawan": "Johan Pahlawan",
                            "Pante Ceureumen": "Pante Ceureumen"
                        }
                    },
                    "Kab. Pidie": {
                        "items": []
                    },
                    "Kab. Aceh Tengah": {
                        "items": []
                    },
                    "Kab. Aceh Selatan": {
                        "items": []
                    },
                    "Kota Sabang": {
                        "items": []
                    },
                    "Kab. Aceh Barat Daya": {
                        "items": {
                            "Blangpidie": "Blangpidie",
                            "Susoh": "Susoh",
                            "Tangan Tangan": "Tangan Tangan"
                        }
                    },
                    "Kab. Aceh Jaya": {
                        "items": {
                            "Krueng Sabee/Calang": "Krueng Sabee/Calang",
                            "Jaya": "Jaya",
                            "Pangat": "Pangat",
                            "Sampo Niet": "Sampo Niet",
                            "Teunom": "Teunom"
                        }
                    },
                    "Kab. Aceh Singkil": {
                        "items": []
                    },
                    "Kab. Aceh Tamiang": {
                        "items": []
                    },
                    "Kab. Aceh Timur": {
                        "items": []
                    },
                    "Kab. Aceh Utara": {
                        "items": []
                    },
                    "Kab. Bener Meriah": {
                        "items": []
                    },
                    "Kab. Nagan Raya": {
                        "items": []
                    },
                    "Kab. Pidie Jaya": {
                        "items": []
                    },
                    "Kab. Simeulue": {
                        "items": []
                    },
                    "Kota Subulussalam": {
                        "items": []
                    },
                    "Kota Medan": {
                        "items": []
                    },
                    "Kab. Asahan": {
                        "items": {
                            "Kisaran": "Kisaran",
                            "Kisaran Timur": "Kisaran Timur"
                        }
                    },
                    "Kab. Tapanuli Tengah": {
                        "items": []
                    },
                    "Kota Sibolga": {
                        "items": []
                    },
                    "Kab. Dairi": {
                        "items": []
                    },
                    "Kab. Deli Serdang": {
                        "items": {
                            "Lubuk Pakam": "Lubuk Pakam",
                            "Deli Tua": "Deli Tua",
                            "Kutalimbaru": "Kutalimbaru",
                            "Percut Sei Tuan": "Percut Sei Tuan",
                            "Sibolangit": "Sibolangit",
                            "Biru-Biru": "Biru-Biru",
                            "Sunggal": "Sunggal",
                            "Pantai Labu": "Pantai Labu",
                            "Beringin": "Beringin",
                            "Namo Rambe": "Namo Rambe",
                            "Pagar Marbau": "Pagar Marbau"
                        }
                    },
                    "Kab. Toba Samosir": {
                        "items": []
                    },
                    "Kota Binjai": {
                        "items": []
                    },
                    "Kab. Nias": {
                        "items": []
                    },
                    "Kab. Simalungun": {
                        "items": []
                    },
                    "Kab. Labuhanbatu": {
                        "items": {
                            "Aek Natas": "Aek Natas",
                            "Kota Pinang": "Kota Pinang",
                            "Marbau": "Marbau",
                            "Na IX-X": "Na IX-X",
                            "Bilah Barat": "Bilah Barat",
                            "Kualuh Selatan": "Kualuh Selatan",
                            "Pangkatan": "Pangkatan",
                            "Rantau Selatan": "Rantau Selatan",
                            "Rantau Utara": "Rantau Utara"
                        }
                    },
                    "Kab. Karo": {
                        "items": []
                    },
                    "Kab. Langkat": {
                        "items": []
                    },
                    "Kab. Tapanuli Utara": {
                        "items": []
                    },
                    "Kota Tebing Tinggi": {
                        "items": []
                    },
                    "Kab. Batubara": {
                        "items": {
                            "Limapuluh": "Limapuluh",
                            "Talawi": "Talawi"
                        }
                    },
                    "Kab. Humbang Hasundutan": {
                        "items": []
                    },
                    "Kab. Samosir": {
                        "items": []
                    },
                    "Kab. Serdang Bedagai": {
                        "items": {
                            "Sei Rampah": "Sei Rampah",
                            "Bandar Khalipah": "Bandar Khalipah",
                            "Dolok Masihul": "Dolok Masihul",
                            "Pantai Cermin": "Pantai Cermin",
                            "Perbaungan": "Perbaungan",
                            "Tanjung Beringin": "Tanjung Beringin",
                            "Tebingtinggi": "Tebingtinggi"
                        }
                    },
                    "Kota Tanjungbalai": {
                        "items": {
                            "Tanjung Balai": "Tanjung Balai",
                            "Datuk Bandar": "Datuk Bandar",
                            "Sei Tualang Raso": "Sei Tualang Raso",
                            "Tanjung Balai Selatan": "Tanjung Balai Selatan",
                            "Tanjung Balai Utara": "Tanjung Balai Utara",
                            "Teluk Nibung": "Teluk Nibung"
                        }
                    },
                    "Kota Pematangsiantar": {
                        "items": []
                    },
                    "Kota Padangsidempuan": {
                        "items": []
                    },
                    "Kota Padang": {
                        "items": {
                            "Padang": "Padang",
                            "Bungus Teluk Kabung": "Bungus Teluk Kabung",
                            "Koto Tangah": "Koto Tangah",
                            "Kuranji": "Kuranji",
                            "Lubuk Begalung": "Lubuk Begalung",
                            "Lubuk Kilangan": "Lubuk Kilangan",
                            "Nanggalo": "Nanggalo",
                            "Padang Barat": "Padang Barat",
                            "Padang Selatan": "Padang Selatan",
                            "Padang Timur": "Padang Timur"
                        }
                    },
                    "Kab. Tanah Datar": {
                        "items": {
                            "Batu Sangkar": "Batu Sangkar",
                            "Batipuh": "Batipuh",
                            "Lintau Buo": "Lintau Buo",
                            "Pariangan": "Pariangan",
                            "Rambatan": "Rambatan",
                            "Salimpaung": "Salimpaung",
                            "Sungai Tarab": "Sungai Tarab",
                            "Sungayang": "Sungayang",
                            "Tanjung Emas": "Tanjung Emas",
                            "Batipuh Selatan": "Batipuh Selatan",
                            "Lima Kaum": "Lima Kaum",
                            "Lintau Buo Utara": "Lintau Buo Utara",
                            "Padang Ganting": "Padang Ganting",
                            "Sepuluh Koto": "Sepuluh Koto",
                            "Tanjung Baru": "Tanjung Baru"
                        }
                    },
                    "Kota Bukittinggi": {
                        "items": {
                            "Bukit Tinggi": "Bukit Tinggi",
                            "Aur Birugo Tigo Baleh": "Aur Birugo Tigo Baleh",
                            "Mandiangin Koto Selayan": "Mandiangin Koto Selayan",
                            "Guguk Panjang": "Guguk Panjang"
                        }
                    },
                    "Kab. Pasaman": {
                        "items": []
                    },
                    "Kab. Pesisir Selatan": {
                        "items": []
                    },
                    "Kab. Padang Pariaman": {
                        "items": []
                    },
                    "Kota Pariaman": {
                        "items": []
                    },
                    "Kota Payakumbuh": {
                        "items": []
                    },
                    "Kota Sawahlunto": {
                        "items": []
                    },
                    "Kota Solok": {
                        "items": {
                            "Solok": "Solok",
                            "Lubuk Sikarah": "Lubuk Sikarah"
                        }
                    },
                    "Kota Padangpanjang": {
                        "items": {
                            "Padang Panjang": "Padang Panjang",
                            "Padang Panjang Barat": "Padang Panjang Barat",
                            "Padang Panjang Timur": "Padang Panjang Timur"
                        }
                    },
                    "Kab. Dharmasraya": {
                        "items": []
                    },
                    "Kab. Lima Puluh Kota": {
                        "items": []
                    },
                    "Kab. Kepulauan Mentawai": {
                        "items": []
                    },
                    "Kab. Pasaman Barat": {
                        "items": []
                    },
                    "Kab. Solok": {
                        "items": []
                    },
                    "Kab. Solok Selatan": {
                        "items": []
                    },
                    "Kab. Sijunjung": {
                        "items": []
                    },
                    "Kab. Agam": {
                        "items": {
                            "Lubuk Basung": "Lubuk Basung",
                            "Tanjung Mutiara": "Tanjung Mutiara",
                            "Tanjung Raya": "Tanjung Raya",
                            "IV Angkat Candung": "IV Angkat Candung",
                            "Banuhampu": "Banuhampu",
                            "Baso": "Baso",
                            "IV Koto": "IV Koto",
                            "Candung": "Candung",
                            "Matur": "Matur",
                            "Tilatang Kamang": "Tilatang Kamang",
                            "Ampek Nagari": "Ampek Nagari",
                            "Kamang Magek": "Kamang Magek",
                            "Sungai Puar": "Sungai Puar"
                        }
                    },
                    "Kota Pekanbaru": {
                        "items": {
                            "Pekanbaru": "Pekanbaru",
                            "Payung": "Payung",
                            "Lima Puluh": "Lima Puluh",
                            "Marpoyan Damai": "Marpoyan Damai",
                            "Sail": "Sail",
                            "Senapelan": "Senapelan",
                            "Sukajadi": "Sukajadi",
                            "Bukit Raya": "Bukit Raya",
                            "Tampan": "Tampan",
                            "Rumbai Pesisir": "Rumbai Pesisir",
                            "Tenayan Raya": "Tenayan Raya",
                            "Rumbai": "Rumbai"
                        }
                    },
                    "Kota Dumai": {
                        "items": []
                    },
                    "Kab. Kampar": {
                        "items": []
                    },
                    "Kab. Bengkalis": {
                        "items": {
                            "Bengkalis": "Bengkalis",
                            "Bukit Batu": "Bukit Batu",
                            "Mandau": "Mandau",
                            "Bantan": "Bantan",
                            "Siak Kecil": "Siak Kecil",
                            "Duri Mandau": "Duri Mandau"
                        }
                    },
                    "Kab. Indragiri Hulu": {
                        "items": {
                            "Rengat": "Rengat",
                            "Pasir Penyu": "Pasir Penyu",
                            "Seberida": "Seberida",
                            "Lirik": "Lirik"
                        }
                    },
                    "Kab. Indragiri Hilir": {
                        "items": []
                    },
                    "Kab. Rokan Hilir": {
                        "items": []
                    },
                    "Kab. Kuantan Singingi": {
                        "items": []
                    },
                    "Kab. Pelalawan": {
                        "items": []
                    },
                    "Kab. Rokan Hulu": {
                        "items": []
                    },
                    "Kab. Siak": {
                        "items": []
                    },
                    "Kab. Kepulauan Meranti": {
                        "items": []
                    },
                    "Kota Batam": {
                        "items": {
                            "Batam": "Batam",
                            "Sekupang/ Tanjung Ucang": "Sekupang/ Tanjung Ucang",
                            "Lubuk Baja": "Lubuk Baja",
                            "Sei Beduk": "Sei Beduk"
                        }
                    },
                    "Kab. Lingga": {
                        "items": []
                    },
                    "Kab. Karimun": {
                        "items": {
                            "Tanjung Balai Karimun": "Tanjung Balai Karimun",
                            "Karimun": "Karimun",
                            "Meral": "Meral",
                            "Tebing": "Tebing"
                        }
                    },
                    "Kota Tanjung Pinang": {
                        "items": {
                            "Tanjung Pinang": "Tanjung Pinang",
                            "Bukit Bestari": "Bukit Bestari",
                            "Tanjung Pinang Barat": "Tanjung Pinang Barat",
                            "Tanjung Pinang Kota": "Tanjung Pinang Kota",
                            "Tanjung Pinang Timur": "Tanjung Pinang Timur"
                        }
                    },
                    "Kab. Bintan": {
                        "items": {
                            "Bintan Timur": "Bintan Timur"
                        }
                    },
                    "Kota Jambi": {
                        "items": {
                            "Jambi": "Jambi",
                            "Danau Teluk": "Danau Teluk",
                            "Jambi Selatan": "Jambi Selatan",
                            "Jambi Timur": "Jambi Timur",
                            "Jelutung": "Jelutung",
                            "Pasar Jambi": "Pasar Jambi",
                            "Pelayangan": "Pelayangan",
                            "Telanaipura": "Telanaipura"
                        }
                    },
                    "Kab. Merangin": {
                        "items": []
                    },
                    "Kab. Batanghari": {
                        "items": []
                    },
                    "Kab. Bungo": {
                        "items": []
                    },
                    "Kab. Kerinci": {
                        "items": []
                    },
                    "Kab. Muaro Jambi": {
                        "items": []
                    },
                    "Kab. Sarolangun": {
                        "items": []
                    },
                    "Kab. Tanjung Jabung Timur": {
                        "items": {
                            "Muara sabak": "Muara sabak"
                        }
                    },
                    "Kab. Tebo": {
                        "items": []
                    },
                    "Kota Palembang": {
                        "items": {
                            "Palembang": "Palembang",
                            "Plaju": "Plaju",
                            "Alang-Alang Lebar": "Alang-Alang Lebar",
                            "Bukit Kecil": "Bukit Kecil",
                            "Gandus": "Gandus",
                            "Ilir Barat I": "Ilir Barat I",
                            "Ilir Barat II": "Ilir Barat II",
                            "Ilir Timur I": "Ilir Timur I",
                            "Ilir Timur II": "Ilir Timur II",
                            "Kalidoni": "Kalidoni",
                            "Kemuning": "Kemuning",
                            "Kertapati": "Kertapati",
                            "Sako": "Sako",
                            "Seberang Ulu I": "Seberang Ulu I",
                            "Seberang Ulu II": "Seberang Ulu II",
                            "Sematang Borang": "Sematang Borang",
                            "Sukarame": "Sukarame"
                        }
                    },
                    "Kab. Musi Rawas": {
                        "items": []
                    },
                    "Kab. Ogan Komering Ulu": {
                        "items": []
                    },
                    "Kab. Ogan Komering Ilir": {
                        "items": {
                            "Kota Kayu Agung": "Kota Kayu Agung",
                            "Lempuing": "Lempuing",
                            "Pematang Panggang": "Pematang Panggang"
                        }
                    },
                    "Kab. Lahat": {
                        "items": {
                            "Lahat": "Lahat",
                            "Merapi": "Merapi",
                            "Pulau Pinang": "Pulau Pinang",
                            "Kikim Barat": "Kikim Barat",
                            "Kikim Selatan": "Kikim Selatan",
                            "Kikim Tengah": "Kikim Tengah",
                            "Kikim Timur": "Kikim Timur",
                            "Kota Agung": "Kota Agung",
                            "Mulak Ulu": "Mulak Ulu"
                        }
                    },
                    "Kab. Muara Enim": {
                        "items": []
                    },
                    "Kota Pagar Alam": {
                        "items": []
                    },
                    "Kota Prabumulih": {
                        "items": []
                    },
                    "Kab. Musi Banyuasin": {
                        "items": []
                    },
                    "Kab. Banyuasin": {
                        "items": []
                    },
                    "Kab. Empat Lawang": {
                        "items": []
                    },
                    "Kab. Ogan Ilir": {
                        "items": []
                    },
                    "Kab. Ogan Komering Ulu Timur": {
                        "items": []
                    },
                    "Kab. Ogan Komering Ulu Selatan": {
                        "items": []
                    },
                    "Kota Lubuklinggau": {
                        "items": {
                            "Lubuk Linggau": "Lubuk Linggau",
                            "Lubuklinggau Barat I": "Lubuklinggau Barat I",
                            "Lubuklinggau Barat II": "Lubuklinggau Barat II",
                            "Lubuklinggau Selatan I": "Lubuklinggau Selatan I",
                            "Lubuklinggau Selatan II": "Lubuklinggau Selatan II",
                            "Lubuklinggau Timur I": "Lubuklinggau Timur I",
                            "Lubuklinggau Timur II": "Lubuklinggau Timur II",
                            "Lubuklinggau Utara I": "Lubuklinggau Utara I",
                            "Lubuklinggau Utara II": "Lubuklinggau Utara II"
                        }
                    },
                    "Kota Pangkal Pinang": {
                        "items": []
                    },
                    "Kab. Bangka": {
                        "items": []
                    },
                    "Kab. Bangka Barat": {
                        "items": []
                    },
                    "Kab. Bangka Tengah": {
                        "items": []
                    },
                    "Kab. Bangka Selatan": {
                        "items": []
                    },
                    "Kab. Belitung": {
                        "items": []
                    },
                    "Kab. Belitung Timur": {
                        "items": []
                    },
                    "Kota Bengkulu": {
                        "items": {
                            "Bengkulu": "Bengkulu",
                            "Gading Cempaka": "Gading Cempaka",
                            "Kampung Melayu": "Kampung Melayu",
                            "Muara Bangka hulu": "Muara Bangka hulu",
                            "Ratu Agung": "Ratu Agung"
                        }
                    },
                    "Kab. Bengkulu Utara": {
                        "items": []
                    },
                    "Kab. Rejang Lebong": {
                        "items": {
                            "Curup": "Curup",
                            "Selupu Rejang": "Selupu Rejang"
                        }
                    },
                    "Kab. Bengkulu Selatan": {
                        "items": []
                    },
                    "Kab. Kaur": {
                        "items": []
                    },
                    "Kab. Kepahiang": {
                        "items": []
                    },
                    "Kab. Lebong": {
                        "items": []
                    },
                    "Kab. Mukomuko": {
                        "items": []
                    },
                    "Kab. Seluma": {
                        "items": []
                    },
                    "Kota Bandar Lampung": {
                        "items": {
                            "Bandar Lampung": "Bandar Lampung",
                            "Kedaton": "Kedaton",
                            "Kemiling": "Kemiling",
                            "Rajabasa": "Rajabasa",
                            "Tanjung Karang Barat": "Tanjung Karang Barat",
                            "Tanjung Karang Pusat": "Tanjung Karang Pusat",
                            "Tanjung Karang Timur": "Tanjung Karang Timur",
                            "Tanjung Senang": "Tanjung Senang",
                            "Teluk Betung Barat": "Teluk Betung Barat",
                            "Teluk Betung Selatan": "Teluk Betung Selatan",
                            "Teluk Betung Utara": "Teluk Betung Utara"
                        }
                    },
                    "Kab. Lampung Selatan": {
                        "items": {
                            "Kalianda": "Kalianda",
                            "Palas": "Palas",
                            "Natar": "Natar",
                            "Tanjung Bintang": "Tanjung Bintang",
                            "Candipuro": "Candipuro",
                            "Jati Agung": "Jati Agung",
                            "Katibung": "Katibung",
                            "Merbau Mataram": "Merbau Mataram",
                            "Sidomulyo": "Sidomulyo",
                            "Sragi": "Sragi",
                            "Bakauheuni": "Bakauheuni",
                            "Tanjungsari": "Tanjungsari",
                            "Way Panji": "Way Panji",
                            "Way Sulan": "Way Sulan"
                        }
                    },
                    "Kab. Lampung Utara": {
                        "items": {
                            "Kotabumi": "Kotabumi",
                            "Bukit Kemuning": "Bukit Kemuning",
                            "Kotabumi Selatan": "Kotabumi Selatan",
                            "Kotabumi Utara": "Kotabumi Utara"
                        }
                    },
                    "Kota Metro": {
                        "items": {
                            "Metro": "Metro",
                            "Metro Barat": "Metro Barat",
                            "Metro Pusat": "Metro Pusat",
                            "Metro Selatan": "Metro Selatan",
                            "Metro Timur": "Metro Timur",
                            "Metro Utara": "Metro Utara"
                        }
                    },
                    "Kab. Lampung Barat": {
                        "items": {
                            "Balik Bukit": "Balik Bukit",
                            "Belalau": "Belalau",
                            "Sumber Jaya": "Sumber Jaya",
                            "Batu Brak": "Batu Brak",
                            "Sekincau": "Sekincau",
                            "Sukau": "Sukau",
                            "Way Tenong": "Way Tenong"
                        }
                    },
                    "Kab. Lampung Tengah": {
                        "items": {
                            "Gunung Sugih": "Gunung Sugih",
                            "Seputih Mataram": "Seputih Mataram",
                            "Terbanggi Besar / Bandar Jaya": "Terbanggi Besar / Bandar Jaya",
                            "Bandar Mataram": "Bandar Mataram",
                            "Bangunrejo": "Bangunrejo",
                            "Bekri": "Bekri",
                            "Bumi Ratu Nuban": "Bumi Ratu Nuban",
                            "Pubian": "Pubian",
                            "Selagai Lingga": "Selagai Lingga",
                            "Sendang Agung": "Sendang Agung",
                            "Seputih Agung": "Seputih Agung",
                            "Terusan Nunyai": "Terusan Nunyai"
                        }
                    },
                    "Kab. Lampung Timur": {
                        "items": []
                    },
                    "Kab. Tulang Bawang": {
                        "items": []
                    },
                    "Kab. Tanggamus": {
                        "items": []
                    },
                    "Kab. Pesawaran": {
                        "items": []
                    },
                    "DKI Jakarta": {
                        "items": {
                            "Jakarta": "Jakarta"
                        }
                    },
                    "Kota Administrasi Jakarta Barat": {
                        "items": {
                            "Jakarta Barat": "Jakarta Barat",
                            "Kalideres": "Kalideres",
                            "Kebon Jeruk": "Kebon Jeruk",
                            "Kembangan": "Kembangan",
                            "Palmerah": "Palmerah",
                            "Taman Sari": "Taman Sari",
                            "Tambora": "Tambora",
                            "Cengkareng": "Cengkareng"
                        }
                    },
                    "Kota Administrasi Jakarta Selatan": {
                        "items": {
                            "Jakarta Selatan": "Jakarta Selatan",
                            "Jagakarsa": "Jagakarsa",
                            "Kebayoran Baru": "Kebayoran Baru",
                            "Kebayoran Lama": "Kebayoran Lama",
                            "Mampang Prapatan": "Mampang Prapatan",
                            "Pancoran": "Pancoran",
                            "Pasar Minggu": "Pasar Minggu",
                            "Pesanggrahan": "Pesanggrahan",
                            "Setiabudi": "Setiabudi",
                            "Tebet": "Tebet",
                            "Cilandak": "Cilandak"
                        }
                    },
                    "Kota Administrasi Jakarta Pusat": {
                        "items": {
                            "Jakarta Pusat": "Jakarta Pusat",
                            "Cempaka Putih": "Cempaka Putih",
                            "Gambir": "Gambir",
                            "Johar Baru": "Johar Baru",
                            "Kemayoran": "Kemayoran",
                            "Menteng": "Menteng",
                            "Sawah Besar": "Sawah Besar",
                            "Senen": "Senen",
                            "Tanah Abang": "Tanah Abang"
                        }
                    },
                    "Kota Administrasi Jakarta Utara": {
                        "items": {
                            "Jakarta Utara": "Jakarta Utara",
                            "Kelapa Gading": "Kelapa Gading",
                            "Koja": "Koja",
                            "Pademangan": "Pademangan",
                            "Penjaringan": "Penjaringan",
                            "Tanjung Priok": "Tanjung Priok",
                            "Cilincing": "Cilincing"
                        }
                    },
                    "Kota Administrasi Jakarta Timur": {
                        "items": {
                            "Jakarta Timur": "Jakarta Timur",
                            "Cakung": "Cakung",
                            "Cipayung": "Cipayung",
                            "Ciracas": "Ciracas",
                            "Duren Sawit": "Duren Sawit",
                            "Jatinegara": "Jatinegara",
                            "Kramat Jati": "Kramat Jati",
                            "Makassar": "Makassar",
                            "Matraman": "Matraman",
                            "Pasar Rebo": "Pasar Rebo",
                            "Pulo Gadung": "Pulo Gadung"
                        }
                    },
                    "Kota Bandung": {
                        "items": {
                            "Bandung": "Bandung",
                            "Cibiru": "Cibiru",
                            "Ujungberung": "Ujungberung",
                            "Sukasari": "Sukasari",
                            "Cicadas": "Cicadas",
                            "Cidadap": "Cidadap",
                            "Lengkong": "Lengkong",
                            "Andir": "Andir",
                            "Arcamanik": "Arcamanik",
                            "Astanaanyar": "Astanaanyar",
                            "Babakanciparay": "Babakanciparay",
                            "Bandung Kidul": "Bandung Kidul",
                            "Bandung Kulon": "Bandung Kulon",
                            "Bandung Wetan": "Bandung Wetan",
                            "Batununggal": "Batununggal",
                            "Bojongloa Kaler": "Bojongloa Kaler",
                            "Bojongloa Kidul": "Bojongloa Kidul",
                            "Cibeunying Kaler": "Cibeunying Kaler",
                            "Cibeunying Kidul": "Cibeunying Kidul",
                            "Cicendo": "Cicendo",
                            "Coblong": "Coblong",
                            "Kiaracondong": "Kiaracondong",
                            "Margacinta": "Margacinta",
                            "Rancasari": "Rancasari",
                            "Regol": "Regol",
                            "Sukajadi": "Sukajadi",
                            "Sumurbandung": "Sumurbandung"
                        }
                    },
                    "Kab. Bandung": {
                        "items": {
                            "Soreang": "Soreang",
                            "Arjasari": "Arjasari",
                            "Baleendah": "Baleendah",
                            "Banjaran": "Banjaran",
                            "Bojong Soang": "Bojong Soang",
                            "Cangkuang": "Cangkuang",
                            "Cicalengka": "Cicalengka",
                            "Cikancung": "Cikancung",
                            "Cilengkrang": "Cilengkrang",
                            "Cileunyi": "Cileunyi",
                            "Cimaung": "Cimaung",
                            "Cimenyan": "Cimenyan",
                            "Ciparay": "Ciparay",
                            "Ciwidey": "Ciwidey",
                            "Dayeuhkolot": "Dayeuhkolot",
                            "Ibun": "Ibun",
                            "Katapang": "Katapang",
                            "Majalaya": "Majalaya",
                            "Margaasih": "Margaasih",
                            "Margahayu": "Margahayu",
                            "Nagreg": "Nagreg",
                            "Pacet": "Pacet",
                            "Pameungpeuk": "Pameungpeuk",
                            "Pangalengan": "Pangalengan",
                            "Paseh": "Paseh",
                            "Pasirjambu": "Pasirjambu",
                            "Rancaekek": "Rancaekek",
                            "Solokan Jeruk": "Solokan Jeruk"
                        }
                    },
                    "Kota Cimahi": {
                        "items": {
                            "Cimahi": "Cimahi",
                            "Cimahi Selatan": "Cimahi Selatan",
                            "Cimahi Tengah": "Cimahi Tengah",
                            "Cimahi Utara": "Cimahi Utara"
                        }
                    },
                    "Kab. Sumedang": {
                        "items": {
                            "Sumedang": "Sumedang",
                            "Buahdua": "Buahdua",
                            "Cibugel": "Cibugel",
                            "Cimalaka": "Cimalaka",
                            "Conggeang": "Conggeang",
                            "Darmaraja": "Darmaraja",
                            "Ganeas": "Ganeas",
                            "Paseh": "Paseh",
                            "Rancakalong": "Rancakalong",
                            "Situraja": "Situraja",
                            "Tanjungkerta": "Tanjungkerta",
                            "Tomo": "Tomo",
                            "Ujung Jaya": "Ujung Jaya",
                            "Wado": "Wado",
                            "Cisitu": "Cisitu",
                            "Jatigede": "Jatigede",
                            "Sumedang Selatan": "Sumedang Selatan",
                            "Sumedang Utara": "Sumedang Utara",
                            "Tanjungmedar": "Tanjungmedar"
                        }
                    },
                    "Kota Tasikmalaya": {
                        "items": {
                            "Tasikmalaya": "Tasikmalaya",
                            "Indihiang": "Indihiang",
                            "Kawalu": "Kawalu",
                            "Cihideung": "Cihideung",
                            "Cipedes": "Cipedes",
                            "Mangkubumi": "Mangkubumi",
                            "Tawang": "Tawang"
                        }
                    },
                    "Kota Banjar": {
                        "items": {
                            "Banjar": "Banjar",
                            "Langensari": "Langensari",
                            "Pataruman": "Pataruman",
                            "Purwaharja": "Purwaharja"
                        }
                    },
                    "Kab. Ciamis": {
                        "items": {
                            "Ciamis": "Ciamis",
                            "Cihaurbeuti": "Cihaurbeuti",
                            "Cijeungjing": "Cijeungjing",
                            "Cikoneng": "Cikoneng",
                            "Cipaku": "Cipaku",
                            "Jatinagara": "Jatinagara",
                            "Kawali": "Kawali",
                            "Panumbangan": "Panumbangan",
                            "Panawangan": "Panawangan",
                            "Panjalu": "Panjalu",
                            "Rajadesa": "Rajadesa",
                            "Sadananya": "Sadananya",
                            "Tambaksari": "Tambaksari",
                            "Baregbeg": "Baregbeg",
                            "Lumbung": "Lumbung",
                            "Sindangkasih": "Sindangkasih"
                        }
                    },
                    "Kab. Tasikmalaya": {
                        "items": []
                    },
                    "Kab. Garut": {
                        "items": {
                            "Garut": "Garut",
                            "Banyuresmi": "Banyuresmi",
                            "Bayongbong": "Bayongbong",
                            "Balubur Limbangan": "Balubur Limbangan",
                            "Cibatu": "Cibatu",
                            "Cibiuk": "Cibiuk",
                            "Cikajang": "Cikajang",
                            "Cilawu": "Cilawu",
                            "Cisurupan": "Cisurupan",
                            "Kadungora": "Kadungora",
                            "Karangpawitan": "Karangpawitan",
                            "Leuwigoong": "Leuwigoong",
                            "Malangbong": "Malangbong",
                            "Samarang": "Samarang",
                            "Selaawi": "Selaawi",
                            "Sukawening": "Sukawening",
                            "Wanaraja": "Wanaraja",
                            "Cigedug": "Cigedug",
                            "Kersamanah": "Kersamanah",
                            "Pangatikan": "Pangatikan",
                            "Pasirwangi": "Pasirwangi",
                            "Sucinaraja": "Sucinaraja",
                            "Tarogong Kaler": "Tarogong Kaler",
                            "Tarogong Kidul": "Tarogong Kidul",
                            "Garut Kota": "Garut Kota"
                        }
                    },
                    "Kab. Purwakarta": {
                        "items": []
                    },
                    "Kab. Bandung Barat": {
                        "items": {
                            "Ngamprah": "Ngamprah",
                            "Batujajar": "Batujajar",
                            "Cikalong Wetan": "Cikalong Wetan",
                            "Cililin": "Cililin",
                            "Cipatat": "Cipatat",
                            "Cisarua": "Cisarua",
                            "Padalarang": "Padalarang",
                            "Cihampelas": "Cihampelas",
                            "Parongpong": "Parongpong"
                        }
                    },
                    "Kab. Subang": {
                        "items": {
                            "Subang": "Subang",
                            "Binong": "Binong",
                            "Blanakan": "Blanakan",
                            "Ciasem": "Ciasem",
                            "Cibogo": "Cibogo",
                            "Cijambe": "Cijambe",
                            "Cisalak": "Cisalak",
                            "Compreng": "Compreng",
                            "Jalan Cagak": "Jalan Cagak",
                            "Kalijati": "Kalijati",
                            "Pamanukan": "Pamanukan",
                            "Patok Beusi": "Patok Beusi",
                            "Pusakanagara": "Pusakanagara",
                            "Sagalaherang": "Sagalaherang",
                            "Tanjung Siang": "Tanjung Siang",
                            "Cikaum": "Cikaum",
                            "Cipunagara": "Cipunagara",
                            "Pagaden": "Pagaden"
                        }
                    },
                    "Kota Bekasi": {
                        "items": {
                            "Bekasi": "Bekasi",
                            "Bantar Gebang": "Bantar Gebang",
                            "Bekasi Barat": "Bekasi Barat",
                            "Bekasi Selatan": "Bekasi Selatan",
                            "Bekasi Timur": "Bekasi Timur",
                            "Bekasi Utara": "Bekasi Utara",
                            "Jatiasih": "Jatiasih",
                            "Jatisampurna": "Jatisampurna",
                            "Medan Satria": "Medan Satria",
                            "Mustika Jaya": "Mustika Jaya",
                            "Pondokgede": "Pondokgede",
                            "Pondok Melati": "Pondok Melati",
                            "Rawalumbu": "Rawalumbu"
                        }
                    },
                    "Kota Bogor": {
                        "items": {
                            "Bogor": "Bogor",
                            "Bogor Barat": "Bogor Barat",
                            "Bogor Selatan": "Bogor Selatan",
                            "Bogor Tengah": "Bogor Tengah",
                            "Bogor Timur": "Bogor Timur",
                            "Bogor Utara": "Bogor Utara",
                            "Tanah Sereal": "Tanah Sereal"
                        }
                    },
                    "Kab. Bogor": {
                        "items": {
                            "Cibinong": "Cibinong",
                            "Caringin": "Caringin",
                            "Cariu": "Cariu",
                            "Ciawi": "Ciawi",
                            "Ciampea": "Ciampea",
                            "Cibungbulang": "Cibungbulang",
                            "Cigudeg": "Cigudeg",
                            "Cijeruk": "Cijeruk",
                            "Cileungsi": "Cileungsi",
                            "Ciomas": "Ciomas",
                            "Citeureup": "Citeureup",
                            "Dramaga": "Dramaga",
                            "Gunung Sindur": "Gunung Sindur",
                            "Jasinga": "Jasinga",
                            "Jonggol": "Jonggol",
                            "Leuwiliang": "Leuwiliang",
                            "Megamendung": "Megamendung",
                            "Nanggung": "Nanggung",
                            "Parung": "Parung",
                            "Parung Panjang": "Parung Panjang",
                            "Rumpin": "Rumpin",
                            "Tenjo": "Tenjo",
                            "Babakan Madang": "Babakan Madang",
                            "Bojonggede": "Bojonggede",
                            "Cigombong": "Cigombong",
                            "Ciseeng": "Ciseeng",
                            "Kemang": "Kemang",
                            "Klapanunggal": "Klapanunggal",
                            "Leuwisadeng": "Leuwisadeng",
                            "Pamijahan": "Pamijahan",
                            "Ranca Bungur": "Ranca Bungur",
                            "Sukajaya": "Sukajaya",
                            "Sukamakmur": "Sukamakmur",
                            "Sukaraja": "Sukaraja",
                            "Tajurhalang": "Tajurhalang",
                            "Tamansari": "Tamansari",
                            "Tenjolaya": "Tenjolaya",
                            "Gunung Putri": "Gunung Putri"
                        }
                    },
                    "Kota Cirebon": {
                        "items": {
                            "Cirebon": "Cirebon",
                            "Harjamukti": "Harjamukti",
                            "Kesambi": "Kesambi"
                        }
                    },
                    "Kab. Indramayu": {
                        "items": {
                            "Indramayu": "Indramayu",
                            "Anjatan": "Anjatan",
                            "Balongan": "Balongan",
                            "Bangodua": "Bangodua",
                            "Bongas": "Bongas",
                            "Cikedung": "Cikedung",
                            "Gabus Wetan": "Gabus Wetan",
                            "Haurgeulis": "Haurgeulis",
                            "Jatibarang": "Jatibarang",
                            "Kandanghaur": "Kandanghaur",
                            "Karangampel": "Karangampel",
                            "Kertasemaya": "Kertasemaya",
                            "Krangkeng": "Krangkeng",
                            "Kroya": "Kroya",
                            "Lelea": "Lelea",
                            "Lohbener": "Lohbener",
                            "Losarang": "Losarang",
                            "Sliyeg": "Sliyeg",
                            "Sukra": "Sukra",
                            "Widasari": "Widasari",
                            "Arahan": "Arahan",
                            "Cantigi": "Cantigi",
                            "Gantar": "Gantar",
                            "Kedokan Bunder": "Kedokan Bunder",
                            "Sindang": "Sindang",
                            "Sukagumiwang": "Sukagumiwang",
                            "Trisi": "Trisi"
                        }
                    },
                    "Kab. Kuningan": {
                        "items": {
                            "Kuningan": "Kuningan",
                            "Ciawi Gebang": "Ciawi Gebang",
                            "Cibingbin": "Cibingbin",
                            "Cibeureum": "Cibeureum",
                            "Cidahu": "Cidahu",
                            "Cigugur": "Cigugur",
                            "Cilimus": "Cilimus",
                            "Cimahi": "Cimahi",
                            "Ciniru": "Ciniru",
                            "Cipicung": "Cipicung",
                            "Darma": "Darma",
                            "Garawangi": "Garawangi",
                            "Jalaksana": "Jalaksana",
                            "Japara": "Japara",
                            "Kadugede": "Kadugede",
                            "Kalimanggis": "Kalimanggis",
                            "Karangkancana": "Karangkancana",
                            "Lebakwangi": "Lebakwangi",
                            "Luragung": "Luragung",
                            "Maleber": "Maleber",
                            "Mandirancan": "Mandirancan",
                            "Nusaherang": "Nusaherang",
                            "Pancalang": "Pancalang",
                            "Pasawahan": "Pasawahan",
                            "Sindangagung": "Sindangagung",
                            "Cigandamekar": "Cigandamekar",
                            "Ciwaru": "Ciwaru"
                        }
                    },
                    "Kab. Majalengka": {
                        "items": []
                    },
                    "Kab. Cirebon": {
                        "items": {
                            "Sumber": "Sumber"
                        }
                    },
                    "Kota Depok": {
                        "items": {
                            "Depok": "Depok",
                            "Cimanggis": "Cimanggis",
                            "Beji": "Beji",
                            "Limo": "Limo",
                            "Pancoran Mas": "Pancoran Mas",
                            "Sukmajaya": "Sukmajaya"
                        }
                    },
                    "Kab. Karawang": {
                        "items": []
                    },
                    "Kab. Sukabumi": {
                        "items": {
                            "Sukabumi": "Sukabumi",
                            "Cibadak": "Cibadak"
                        }
                    },
                    "Kota Sukabumi": {
                        "items": {
                            "Baros": "Baros",
                            "LEMBUR SITU": "LEMBUR SITU",
                            "Cikole": "Cikole",
                            "Citamiang": "Citamiang",
                            "GUNUNG PUYUH": "GUNUNG PUYUH",
                            "Warudoyong": "Warudoyong"
                        }
                    },
                    "Kab. Cianjur": {
                        "items": []
                    },
                    "Kota Cilegon": {
                        "items": []
                    },
                    "Kab. Pandeglang": {
                        "items": []
                    },
                    "Kab. Lebak": {
                        "items": {
                            "Rangkasbitung": "Rangkasbitung",
                            "Sajira": "Sajira",
                            "Warunggunung": "Warunggunung",
                            "Curugbitung": "Curugbitung"
                        }
                    },
                    "Kota Serang": {
                        "items": []
                    },
                    "Kab. Serang": {
                        "items": {
                            "Baros": "Baros",
                            "Bojonegara": "Bojonegara",
                            "Carenang": "Carenang",
                            "Cikande": "Cikande",
                            "Cikeusal": "Cikeusal",
                            "Cinangka": "Cinangka",
                            "Ciomas": "Ciomas",
                            "Ciruas": "Ciruas",
                            "Jawilan": "Jawilan",
                            "Kibin": "Kibin",
                            "Kopo": "Kopo",
                            "Kragilan": "Kragilan",
                            "Kramatwatu": "Kramatwatu",
                            "Pabuaran": "Pabuaran",
                            "Padarincang": "Padarincang",
                            "Pamarayan": "Pamarayan",
                            "Petir": "Petir",
                            "Pontang": "Pontang",
                            "Puloampel": "Puloampel",
                            "Tanara": "Tanara",
                            "Tirtayasa": "Tirtayasa",
                            "Tunjung Teja": "Tunjung Teja",
                            "Waringinkurung": "Waringinkurung"
                        }
                    },
                    "Kota Tangerang": {
                        "items": {
                            "Tangerang": "Tangerang",
                            "Batuceper": "Batuceper",
                            "Benda": "Benda",
                            "Cibodas": "Cibodas",
                            "Cipondoh": "Cipondoh",
                            "Jatiuwung": "Jatiuwung",
                            "Karangtengah": "Karangtengah",
                            "Karawaci": "Karawaci",
                            "Larangan": "Larangan",
                            "Neglasari": "Neglasari",
                            "Periuk": "Periuk"
                        }
                    },
                    "Kab. Tangerang": {
                        "items": {
                            "Tigaraksa": "Tigaraksa",
                            "Balaraja": "Balaraja",
                            "Cikupa": "Cikupa",
                            "Cisoka": "Cisoka",
                            "Kronjo": "Kronjo",
                            "Kresek": "Kresek",
                            "Legok": "Legok",
                            "Mauk": "Mauk",
                            "PASAR KEMIS": "PASAR KEMIS",
                            "Pakuhaji": "Pakuhaji",
                            "Rajeg": "Rajeg",
                            "Sepatan /Jatimulya/Cengklong": "Sepatan /Jatimulya/Cengklong",
                            "Teluknaga": "Teluknaga",
                            "Cisauk": "Cisauk",
                            "Jambe": "Jambe",
                            "Jayanti": "Jayanti",
                            "Kosambi /Salembaran jati": "Kosambi /Salembaran jati",
                            "Pagedangan": "Pagedangan",
                            "Panongan": "Panongan",
                            "Sukadiri": "Sukadiri"
                        }
                    },
                    "Kota Tangerang Selatan": {
                        "items": {
                            "Serpong": "Serpong",
                            "Pondok Aren / Jurang Mangu": "Pondok Aren / Jurang Mangu",
                            "Pamulang": "Pamulang",
                            "Ciputat": "Ciputat"
                        }
                    },
                    "Kab. Cilacap": {
                        "items": {
                            "Cilacap": "Cilacap",
                            "Adipala": "Adipala",
                            "Cipari": "Cipari",
                            "Dayeuhluhur": "Dayeuhluhur",
                            "Gandrungmangu": "Gandrungmangu",
                            "Jeruklegi": "Jeruklegi",
                            "Karangpucung": "Karangpucung",
                            "Kawunganten": "Kawunganten",
                            "Kesugihan": "Kesugihan",
                            "Majenang": "Majenang",
                            "Maos": "Maos",
                            "Nusawungu": "Nusawungu",
                            "Sidareja": "Sidareja",
                            "Wanar": "Wanar",
                            "Cilacap Selatan": "Cilacap Selatan",
                            "Cilacap Tengah": "Cilacap Tengah",
                            "Cilacap Utara": "Cilacap Utara",
                            "Patimuan": "Patimuan",
                            "Bantarsari": "Bantarsari",
                            "Kampung Laut": "Kampung Laut"
                        }
                    },
                    "Kota Magelang": {
                        "items": {
                            "Magelang": "Magelang",
                            "Magelang Selatan": "Magelang Selatan",
                            "Magelang Tengah": "Magelang Tengah",
                            "Magelang Utara": "Magelang Utara"
                        }
                    },
                    "Kab. Kebumen": {
                        "items": {
                            "Kebumen": "Kebumen",
                            "Adimulyo": "Adimulyo",
                            "Aliyan": "Aliyan",
                            "Ambal": "Ambal",
                            "Bulupesantren": "Bulupesantren",
                            "Karanggayam": "Karanggayam",
                            "Klirong": "Klirong",
                            "Kutowinangun": "Kutowinangun",
                            "Kuwarasan": "Kuwarasan",
                            "Mirit": "Mirit",
                            "Pejagoan": "Pejagoan",
                            "Petanahan": "Petanahan",
                            "Prembun": "Prembun",
                            "Puring": "Puring",
                            "Sadang": "Sadang",
                            "Sruweng": "Sruweng",
                            "Gombong": "Gombong",
                            "Ayah": "Ayah",
                            "Buayan": "Buayan",
                            "Rowokele": "Rowokele",
                            "Bonorowo": "Bonorowo",
                            "Karangsambung": "Karangsambung",
                            "Padureso": "Padureso",
                            "Poncowarno": "Poncowarno",
                            "Sempor": "Sempor"
                        }
                    },
                    "Kab. Wonosobo": {
                        "items": []
                    },
                    "Kab. Purworejo": {
                        "items": {
                            "Bagelen": "Bagelen",
                            "Banyuurip": "Banyuurip",
                            "Bener": "Bener",
                            "Kaligesing": "Kaligesing",
                            "Loano": "Loano",
                            "Ngombol": "Ngombol",
                            "Purwodadi": "Purwodadi",
                            "Bruno": "Bruno"
                        }
                    },
                    "Kab. Temanggung": {
                        "items": []
                    },
                    "Kab. Magelang": {
                        "items": []
                    },
                    "Kota Surakarta": {
                        "items": {
                            "Solo": "Solo",
                            "Jebres": "Jebres",
                            "Laweyan": "Laweyan",
                            "Pasar Kliwon": "Pasar Kliwon",
                            "Serengan": "Serengan"
                        }
                    },
                    "Kab. Boyolali": {
                        "items": {
                            "Boyolali": "Boyolali",
                            "Ampel": "Ampel",
                            "Andong": "Andong",
                            "Banyudono": "Banyudono",
                            "Cepogo": "Cepogo",
                            "Karanggede": "Karanggede",
                            "Kemusu": "Kemusu",
                            "Klego": "Klego",
                            "Nogosari": "Nogosari",
                            "Sambi": "Sambi",
                            "Sawit": "Sawit",
                            "Selo": "Selo",
                            "Simo": "Simo",
                            "Teras": "Teras",
                            "Musuk": "Musuk",
                            "Mojosongo": "Mojosongo"
                        }
                    },
                    "Kab. Karanganyar": {
                        "items": {
                            "Jatiyoso": "Jatiyoso",
                            "Jenawi": "Jenawi",
                            "Jumantono": "Jumantono",
                            "Jumapolo": "Jumapolo",
                            "Karangpandan": "Karangpandan",
                            "Kebakkramat": "Kebakkramat",
                            "Kerjo": "Kerjo",
                            "Matesih": "Matesih",
                            "Mojogedang": "Mojogedang",
                            "Ngargoyoso": "Ngargoyoso",
                            "Tawangmangu": "Tawangmangu",
                            "Jaten": "Jaten",
                            "Tasikmadu": "Tasikmadu"
                        }
                    },
                    "Kab. Klaten": {
                        "items": {
                            "Klaten": "Klaten",
                            "Bayat": "Bayat",
                            "Cawas": "Cawas",
                            "Ceper": "Ceper",
                            "Delanggu": "Delanggu",
                            "Gantiwarno": "Gantiwarno",
                            "Jatinom": "Jatinom",
                            "Jogonalan": "Jogonalan",
                            "Juwiring": "Juwiring",
                            "Kalikotes": "Kalikotes",
                            "Karangnongko": "Karangnongko",
                            "Karanganom": "Karanganom",
                            "Karangdowo": "Karangdowo",
                            "Kebonarum": "Kebonarum",
                            "Kemalang": "Kemalang",
                            "Manisrenggo": "Manisrenggo",
                            "Pedan": "Pedan",
                            "Polanharjo": "Polanharjo",
                            "Wedi": "Wedi",
                            "Klaten Selatan": "Klaten Selatan",
                            "Klaten Tengah": "Klaten Tengah",
                            "Klaten Utara": "Klaten Utara"
                        }
                    },
                    "Kab. Sragen": {
                        "items": {
                            "Sragen": "Sragen",
                            "Gesi": "Gesi",
                            "Gemolong": "Gemolong",
                            "Jenar": "Jenar",
                            "Kalijambe": "Kalijambe",
                            "Miri": "Miri",
                            "Masaran": "Masaran",
                            "Mondokan": "Mondokan",
                            "Ngrampal": "Ngrampal",
                            "Plupuh": "Plupuh",
                            "Sambung macan": "Sambung macan",
                            "Sambirejo": "Sambirejo",
                            "Sidoharjo": "Sidoharjo",
                            "Sumberlawang": "Sumberlawang",
                            "Tanon": "Tanon",
                            "Tangen": "Tangen",
                            "Karangmalang": "Karangmalang"
                        }
                    },
                    "Kab. Sukoharjo": {
                        "items": {
                            "Sukoharjo": "Sukoharjo",
                            "Nguter": "Nguter",
                            "Polokarto": "Polokarto",
                            "Tawangsari": "Tawangsari",
                            "Baki": "Baki",
                            "Bendosari": "Bendosari"
                        }
                    },
                    "Kab. Wonogiri": {
                        "items": []
                    },
                    "Kota Semarang": {
                        "items": {
                            "Semarang": "Semarang",
                            "Banyumanik": "Banyumanik",
                            "Candisari": "Candisari",
                            "Gajahmungkur": "Gajahmungkur",
                            "Gayamsari": "Gayamsari",
                            "Genuk": "Genuk",
                            "Gunungpati": "Gunungpati",
                            "Ngaliyan": "Ngaliyan",
                            "Pedurungan": "Pedurungan",
                            "Semarang Barat": "Semarang Barat",
                            "Semarang Selatan": "Semarang Selatan",
                            "Semarang Tengah": "Semarang Tengah",
                            "Semarang Timur": "Semarang Timur",
                            "Semarang Utara": "Semarang Utara",
                            "Tembalang": "Tembalang"
                        }
                    },
                    "Kab. Jepara": {
                        "items": []
                    },
                    "Kab. Kudus": {
                        "items": []
                    },
                    "Kota Pekalongan": {
                        "items": {
                            "Pekalongan": "Pekalongan",
                            "Pekalongan Barat": "Pekalongan Barat",
                            "Pekalongan Selatan": "Pekalongan Selatan",
                            "Pekalongan Timur": "Pekalongan Timur",
                            "Pekalongan Utara": "Pekalongan Utara"
                        }
                    },
                    "Kab. Banyumas": {
                        "items": {
                            "Ajibarang": "Ajibarang",
                            "Banyumas": "Banyumas",
                            "Baturaden": "Baturaden",
                            "Cilongok": "Cilongok",
                            "Gumelar": "Gumelar",
                            "Jatilawang": "Jatilawang",
                            "Kalibagor": "Kalibagor",
                            "Karanglewas": "Karanglewas",
                            "Kebasen": "Kebasen",
                            "Kedung banteng": "Kedung banteng",
                            "Kembaran": "Kembaran",
                            "Kemranjen": "Kemranjen",
                            "Lumbir": "Lumbir",
                            "Patikraja": "Patikraja",
                            "Purwojati": "Purwojati",
                            "Rawalo": "Rawalo",
                            "Sokaraja": "Sokaraja",
                            "Somagede": "Somagede",
                            "Sumpyuh /Sumpiuh": "Sumpyuh /Sumpiuh",
                            "Sumbang": "Sumbang",
                            "Wangon": "Wangon",
                            "Purwokerto Barat": "Purwokerto Barat",
                            "Purwokerto Selatan": "Purwokerto Selatan",
                            "Purwokerto Timur": "Purwokerto Timur",
                            "Purwokerto Utara": "Purwokerto Utara"
                        }
                    },
                    "Kab. Batang": {
                        "items": {
                            "Batang": "Batang",
                            "Blado": "Blado",
                            "Limpung": "Limpung",
                            "Subah": "Subah",
                            "Tersono": "Tersono",
                            "Tulis": "Tulis",
                            "Warungasem": "Warungasem",
                            "Wonotunggal": "Wonotunggal",
                            "Kandeman": "Kandeman",
                            "Pecalungan": "Pecalungan"
                        }
                    },
                    "Kab. Blora": {
                        "items": []
                    },
                    "Kab. Brebes": {
                        "items": {
                            "Brebes": "Brebes",
                            "Banjarharjo": "Banjarharjo",
                            "Bantarkawung": "Bantarkawung",
                            "Bulakamba": "Bulakamba",
                            "Bumiayu": "Bumiayu",
                            "Kersana": "Kersana",
                            "Ketanggungan": "Ketanggungan",
                            "Paguyangan": "Paguyangan",
                            "Salem": "Salem",
                            "Sirampog": "Sirampog",
                            "Songgom": "Songgom",
                            "Tonjong": "Tonjong",
                            "Wanasari": "Wanasari"
                        }
                    },
                    "Kab. Demak": {
                        "items": {
                            "Demak": "Demak",
                            "Bonang": "Bonang",
                            "Gajah": "Gajah",
                            "Karangawen": "Karangawen",
                            "Mijen": "Mijen",
                            "Mranggen": "Mranggen",
                            "Sayung": "Sayung",
                            "Wedung": "Wedung",
                            "Kebonagung": "Kebonagung"
                        }
                    },
                    "Kab. Kendal": {
                        "items": {
                            "Kendal": "Kendal",
                            "Boja": "Boja",
                            "Brangsong": "Brangsong",
                            "Cepiring": "Cepiring",
                            "Gemuh": "Gemuh",
                            "Kaliwungu": "Kaliwungu",
                            "Limbangan": "Limbangan",
                            "Pagerruyung": "Pagerruyung",
                            "Patebon": "Patebon",
                            "Patean": "Patean",
                            "Pegandon": "Pegandon",
                            "Weleri": "Weleri",
                            "Kangkung": "Kangkung",
                            "Ngampel": "Ngampel",
                            "Ringinarum": "Ringinarum",
                            "Rowosari /Weleri Utara": "Rowosari /Weleri Utara",
                            "Singorojo": "Singorojo"
                        }
                    },
                    "Kab. Pati": {
                        "items": {
                            "Pati": "Pati",
                            "Batangan": "Batangan",
                            "Cluwak": "Cluwak",
                            "Dukuhseti": "Dukuhseti",
                            "Gembong": "Gembong",
                            "Gunungwungkal": "Gunungwungkal",
                            "Jaken": "Jaken",
                            "Jakenan": "Jakenan",
                            "Juwana": "Juwana",
                            "Kayen": "Kayen",
                            "Margorejo": "Margorejo",
                            "Margoyoso": "Margoyoso",
                            "Pucakwangi": "Pucakwangi",
                            "Tambakromo": "Tambakromo",
                            "Tayu": "Tayu",
                            "Trangkil": "Trangkil",
                            "Wedarijaksa": "Wedarijaksa",
                            "Winong": "Winong"
                        }
                    },
                    "Kab. Pemalang": {
                        "items": []
                    },
                    "Kab. Grobogan": {
                        "items": []
                    },
                    "Kab. Rembang": {
                        "items": {
                            "Rembang": "Rembang",
                            "Kaliori": "Kaliori",
                            "Kragan": "Kragan",
                            "Lasem": "Lasem",
                            "Pamotan": "Pamotan",
                            "Pancur": "Pancur",
                            "Sale": "Sale",
                            "Sarang": "Sarang",
                            "Sedan": "Sedan",
                            "Sluke": "Sluke",
                            "Sulang": "Sulang"
                        }
                    },
                    "Kota Salatiga": {
                        "items": []
                    },
                    "Kab. Tegal": {
                        "items": []
                    },
                    "Kota Tegal": {
                        "items": []
                    },
                    "Kab. Semarang": {
                        "items": []
                    },
                    "Kab. Purbalingga": {
                        "items": []
                    },
                    "Kab. Banjarnegara": {
                        "items": {
                            "Banjarnegara": "Banjarnegara",
                            "Banjarmangu": "Banjarmangu",
                            "Kalibening": "Kalibening",
                            "Karangkobar": "Karangkobar",
                            "Madukara": "Madukara",
                            "Mandiraja": "Mandiraja",
                            "Pagentan": "Pagentan",
                            "Pejawaran": "Pejawaran",
                            "Punggelan": "Punggelan",
                            "Purworejo Klampok": "Purworejo Klampok",
                            "Rakit": "Rakit",
                            "Sigaluh": "Sigaluh",
                            "Wanadadi": "Wanadadi"
                        }
                    },
                    "Kab. Pekalongan": {
                        "items": {
                            "Kajen": "Kajen",
                            "Buaran": "Buaran",
                            "Doro": "Doro",
                            "Karangdadap": "Karangdadap",
                            "Kedungwuni": "Kedungwuni",
                            "Siwalan": "Siwalan",
                            "Tirto": "Tirto",
                            "Wiradesa": "Wiradesa",
                            "Wonokerto": "Wonokerto",
                            "Wonopringgo": "Wonopringgo"
                        }
                    },
                    "Kab. Jember": {
                        "items": []
                    },
                    "Kab. Banyuwangi": {
                        "items": {
                            "Banyuwangi": "Banyuwangi",
                            "Bangorejo": "Bangorejo",
                            "Cluring": "Cluring",
                            "Gambiran": "Gambiran",
                            "Genteng": "Genteng",
                            "Glenmore": "Glenmore",
                            "Kabat": "Kabat",
                            "Kalibaru": "Kalibaru",
                            "Muncar": "Muncar",
                            "Pesanggaran": "Pesanggaran",
                            "Purwoharjo": "Purwoharjo",
                            "Rogojampi": "Rogojampi",
                            "Singojuruh": "Singojuruh",
                            "Songgon": "Songgon",
                            "Srono": "Srono",
                            "Tegaldlimo": "Tegaldlimo",
                            "Wongsorejo": "Wongsorejo",
                            "Glagah": "Glagah",
                            "Giri": "Giri",
                            "Kalipuro": "Kalipuro",
                            "Siliragung": "Siliragung",
                            "Tegalsari": "Tegalsari"
                        }
                    },
                    "Kab. Bondowoso": {
                        "items": []
                    },
                    "Kota Kediri": {
                        "items": []
                    },
                    "Kab. Kediri": {
                        "items": []
                    },
                    "Kota Madiun": {
                        "items": {
                            "Madiun": "Madiun",
                            "Kartoharjo": "Kartoharjo",
                            "Manguharjo": "Manguharjo",
                            "Taman": "Taman"
                        }
                    },
                    "Kab. Madiun": {
                        "items": {
                            "Balerejo": "Balerejo",
                            "Dagangan": "Dagangan",
                            "Dolopo": "Dolopo",
                            "Jiwan": "Jiwan",
                            "Kebonsari": "Kebonsari",
                            "Madiun": "Madiun",
                            "Mejayan": "Mejayan",
                            "Pilangkenceng": "Pilangkenceng",
                            "Saradan": "Saradan",
                            "Sawahan": "Sawahan",
                            "Wonoasri": "Wonoasri",
                            "Wungu": "Wungu"
                        }
                    },
                    "Kab. Magetan": {
                        "items": {
                            "Magetan": "Magetan",
                            "Bendo": "Bendo",
                            "Karangrejo": "Karangrejo",
                            "Kawedanan": "Kawedanan",
                            "Lembeyan": "Lembeyan",
                            "Maospati": "Maospati",
                            "Panekan": "Panekan",
                            "Parang": "Parang",
                            "Plaosan": "Plaosan",
                            "Poncol": "Poncol",
                            "Sukomoro": "Sukomoro",
                            "Takeran": "Takeran",
                            "Barat": "Barat",
                            "Kartoharjo": "Kartoharjo",
                            "Ngariboyo": "Ngariboyo",
                            "Nguntoronadi": "Nguntoronadi"
                        }
                    },
                    "Kab. Ngawi": {
                        "items": []
                    },
                    "Kab. Pacitan": {
                        "items": []
                    },
                    "Kab. Ponorogo": {
                        "items": []
                    },
                    "Kab. Mojokerto": {
                        "items": []
                    },
                    "Kab. Sidoarjo": {
                        "items": {
                            "Balongbendo": "Balongbendo",
                            "Tarik": "Tarik",
                            "Sidoarjo": "Sidoarjo",
                            "Buduran": "Buduran",
                            "Candi": "Candi",
                            "Krembung": "Krembung",
                            "Porong": "Porong",
                            "Sedati": "Sedati",
                            "Tanggulangin": "Tanggulangin",
                            "Tulangan": "Tulangan",
                            "Wonoayu": "Wonoayu",
                            "Krian": "Krian"
                        }
                    },
                    "Kota Mojokerto": {
                        "items": []
                    },
                    "Kota Malang": {
                        "items": {
                            "Malang": "Malang",
                            "Blimbing": "Blimbing",
                            "Kedungkandang": "Kedungkandang",
                            "Klojen": "Klojen",
                            "Lowokwaru": "Lowokwaru",
                            "Sukun": "Sukun"
                        }
                    },
                    "Kab. Blitar": {
                        "items": []
                    },
                    "Kota Blitar": {
                        "items": {
                            "Sananwetan": "Sananwetan",
                            "Sukorejo": "Sukorejo"
                        }
                    },
                    "Kota Batu": {
                        "items": []
                    },
                    "Kab. Malang": {
                        "items": {
                            "Kepanjen": "Kepanjen",
                            "Bululawang": "Bululawang",
                            "Dampit": "Dampit",
                            "Dau": "Dau",
                            "Gondanglegi": "Gondanglegi",
                            "Kalipare": "Kalipare",
                            "Karangploso": "Karangploso",
                            "Kromengan": "Kromengan",
                            "Ngajum": "Ngajum",
                            "Pagak": "Pagak",
                            "Pakis": "Pakis",
                            "Pakisaji": "Pakisaji",
                            "Poncokusumo": "Poncokusumo",
                            "Singosari": "Singosari",
                            "Sumberpucung": "Sumberpucung",
                            "Sumbermanjing Wetan": "Sumbermanjing Wetan",
                            "Tajinan": "Tajinan",
                            "Turen": "Turen",
                            "Tumpang": "Tumpang",
                            "Wagir": "Wagir",
                            "Wajak": "Wajak",
                            "Ngantang": "Ngantang",
                            "Pujon": "Pujon",
                            "Kasembon": "Kasembon",
                            "Lawang": "Lawang"
                        }
                    },
                    "Kab. Probolinggo": {
                        "items": {
                            "Probolinggo": "Probolinggo",
                            "Banyuanyar": "Banyuanyar",
                            "Bantaran": "Bantaran",
                            "Besuk": "Besuk",
                            "Dringu": "Dringu",
                            "Gending": "Gending",
                            "Kotaanyar": "Kotaanyar",
                            "Kraksaan": "Kraksaan",
                            "Krejengan": "Krejengan",
                            "Krucil": "Krucil",
                            "Leces": "Leces",
                            "Maron": "Maron",
                            "Paiton": "Paiton",
                            "Pajarakan": "Pajarakan",
                            "Pakuniran": "Pakuniran",
                            "Sukapura": "Sukapura",
                            "Sumberasih": "Sumberasih",
                            "Tegalsiwalan": "Tegalsiwalan",
                            "Tiris": "Tiris",
                            "Tongas": "Tongas",
                            "Wonomerto": "Wonomerto",
                            "Gading": "Gading"
                        }
                    },
                    "Kota Probolinggo": {
                        "items": {
                            "Mayangan": "Mayangan",
                            "Wonoasih": "Wonoasih",
                            "Kedopok": "Kedopok",
                            "Kanigaran": "Kanigaran"
                        }
                    },
                    "Kab. Lumajang": {
                        "items": {
                            "Lumajang": "Lumajang",
                            "Gucialit": "Gucialit",
                            "Jatiroto": "Jatiroto",
                            "Klakah": "Klakah",
                            "Kedungjajang": "Kedungjajang",
                            "Kunir": "Kunir",
                            "Pasirian": "Pasirian",
                            "Randuagung": "Randuagung",
                            "Ranuyoso": "Ranuyoso",
                            "Rowokangkung": "Rowokangkung",
                            "Senduro": "Senduro",
                            "Sukodono": "Sukodono",
                            "Tekung": "Tekung",
                            "Tempeh": "Tempeh",
                            "Yosowilangun": "Yosowilangun",
                            "Padang": "Padang",
                            "Pasrujambe": "Pasrujambe",
                            "Sumbersoko": "Sumbersoko"
                        }
                    },
                    "Kab. Situbondo": {
                        "items": []
                    },
                    "Kab. Pasuruan": {
                        "items": {
                            "Pandaan": "Pandaan",
                            "Bangil": "Bangil",
                            "Gondang Wetan": "Gondang Wetan",
                            "Grati": "Grati",
                            "Kejayan": "Kejayan",
                            "Kraton": "Kraton",
                            "Lekok": "Lekok",
                            "Nguling": "Nguling",
                            "Pasrepan": "Pasrepan",
                            "Pohjentrek": "Pohjentrek",
                            "Prigen": "Prigen",
                            "Purwodadi": "Purwodadi",
                            "Purwosari": "Purwosari",
                            "Tutur": "Tutur",
                            "Winongan": "Winongan",
                            "Wonorejo": "Wonorejo",
                            "Kedawung": "Kedawung",
                            "Nongko Jajar": "Nongko Jajar"
                        }
                    },
                    "Kota Pasuruan": {
                        "items": []
                    },
                    "Kab. Bojonegoro": {
                        "items": {
                            "Bojonegoro": "Bojonegoro",
                            "Balen": "Balen",
                            "Baureno": "Baureno",
                            "Bubulan": "Bubulan",
                            "Dander": "Dander",
                            "Kalitidu": "Kalitidu",
                            "Kanor": "Kanor",
                            "Kapas": "Kapas",
                            "Kasiman": "Kasiman",
                            "Kedungadem": "Kedungadem",
                            "Kepohbaru": "Kepohbaru",
                            "Malo": "Malo",
                            "Ngraho": "Ngraho",
                            "Padangan": "Padangan",
                            "Sugihwaras": "Sugihwaras",
                            "Tambakrejo": "Tambakrejo",
                            "Temayang": "Temayang",
                            "Trucuk": "Trucuk",
                            "Sukosewu": "Sukosewu",
                            "Gondang": "Gondang"
                        }
                    },
                    "Kota Surabaya": {
                        "items": {
                            "Surabaya": "Surabaya",
                            "Asemrowo": "Asemrowo",
                            "Benowo": "Benowo",
                            "Bubutan": "Bubutan",
                            "Bulak": "Bulak",
                            "Dukuh Pakis": "Dukuh Pakis",
                            "Gayungan": "Gayungan",
                            "Gubeng": "Gubeng",
                            "Gununganyar": "Gununganyar",
                            "Jambangan": "Jambangan",
                            "Karangpilang": "Karangpilang",
                            "Kenjeran": "Kenjeran",
                            "Krembangan": "Krembangan",
                            "Lakarsantri": "Lakarsantri",
                            "Mulyorejo": "Mulyorejo",
                            "Pabean Cantikan": "Pabean Cantikan",
                            "Pakal": "Pakal",
                            "Rungkut": "Rungkut",
                            "Sambikerep": "Sambikerep",
                            "Semampir": "Semampir",
                            "Simokerto": "Simokerto",
                            "Sukolilo": "Sukolilo",
                            "Sukomanunggal": "Sukomanunggal",
                            "Tandes": "Tandes",
                            "Tenggilis Mejoyo": "Tenggilis Mejoyo",
                            "Wiyung": "Wiyung",
                            "Wonocolo": "Wonocolo",
                            "Wonokromo": "Wonokromo"
                        }
                    },
                    "Kab. Gresik": {
                        "items": {
                            "Gresik": "Gresik",
                            "Balongpanggang": "Balongpanggang",
                            "Benjeng": "Benjeng",
                            "Bungah": "Bungah",
                            "Cerme": "Cerme",
                            "Driyorejo": "Driyorejo",
                            "Duduk Sampeyan": "Duduk Sampeyan",
                            "Dukun": "Dukun",
                            "Kedamean": "Kedamean",
                            "Manyar": "Manyar",
                            "Menganti": "Menganti",
                            "Panceng": "Panceng",
                            "Sidayu": "Sidayu",
                            "Tambak": "Tambak",
                            "Ujung Pangkah": "Ujung Pangkah",
                            "Wringinanom": "Wringinanom"
                        }
                    },
                    "Kab. Lamongan": {
                        "items": {
                            "Lamongan": "Lamongan",
                            "Babat": "Babat",
                            "Bluluk": "Bluluk",
                            "Brondong": "Brondong",
                            "Deket": "Deket",
                            "Kalitengah": "Kalitengah",
                            "Karangbinangun": "Karangbinangun",
                            "Karanggeneng": "Karanggeneng",
                            "Kedungpring": "Kedungpring",
                            "Kembangbahu": "Kembangbahu",
                            "Laren": "Laren",
                            "Mantup": "Mantup",
                            "Modo": "Modo",
                            "Ngimbang": "Ngimbang",
                            "Paciran": "Paciran",
                            "Pucuk": "Pucuk",
                            "Sambeng": "Sambeng",
                            "Solokuro": "Solokuro",
                            "Sekaran": "Sekaran",
                            "Sugio": "Sugio",
                            "Sukodadi": "Sukodadi",
                            "Sukorame": "Sukorame",
                            "Tikung": "Tikung",
                            "Maduran": "Maduran",
                            "Sarirejo": "Sarirejo"
                        }
                    },
                    "Kab. Bangkalan": {
                        "items": []
                    },
                    "Kab. Jombang": {
                        "items": []
                    },
                    "Kab. Nganjuk": {
                        "items": []
                    },
                    "Kab. Pamekasan": {
                        "items": {
                            "Pamekasan": "Pamekasan",
                            "Batu Marmar": "Batu Marmar",
                            "Kadur": "Kadur",
                            "Pakong": "Pakong",
                            "Palengaan": "Palengaan",
                            "Pasean": "Pasean",
                            "Pegantenan": "Pegantenan",
                            "Proppo": "Proppo",
                            "Tlanakan": "Tlanakan",
                            "Pademawu": "Pademawu"
                        }
                    },
                    "Kab. Sampang": {
                        "items": []
                    },
                    "Kab. Sumenep": {
                        "items": {
                            "Sumenep": "Sumenep",
                            "Ambunten": "Ambunten",
                            "Batuputih": "Batuputih",
                            "Bluto": "Bluto",
                            "Dasuk": "Dasuk",
                            "Dungkek": "Dungkek",
                            "Ganding": "Ganding",
                            "Gapura": "Gapura",
                            "Guluk-Guluk": "Guluk-Guluk",
                            "Kalianget": "Kalianget",
                            "Lenteng": "Lenteng",
                            "Manding": "Manding",
                            "Pasongsongan": "Pasongsongan",
                            "Pragaan": "Pragaan",
                            "Rubaru": "Rubaru",
                            "Saronggi": "Saronggi",
                            "Talango": "Talango",
                            "Batuan": "Batuan"
                        }
                    },
                    "Kab. Tuban": {
                        "items": {
                            "Tuban": "Tuban",
                            "Bancar": "Bancar",
                            "Bangilan": "Bangilan",
                            "Jatirogo": "Jatirogo",
                            "Jenu": "Jenu",
                            "Kenduruan": "Kenduruan",
                            "Kerek": "Kerek",
                            "Merakurak": "Merakurak",
                            "Montong": "Montong",
                            "Palang": "Palang",
                            "Parengan": "Parengan",
                            "Rengel": "Rengel",
                            "Semanding": "Semanding",
                            "Senori": "Senori",
                            "Singgahan": "Singgahan",
                            "Soko": "Soko",
                            "Tambakboyo": "Tambakboyo",
                            "Widang": "Widang",
                            "Grabagan": "Grabagan"
                        }
                    },
                    "Kab. Tulungagung": {
                        "items": {
                            "Tulungagung": "Tulungagung",
                            "Campurdarat": "Campurdarat",
                            "Kalidawir": "Kalidawir",
                            "Karangrejo": "Karangrejo",
                            "Ngantru": "Ngantru",
                            "Ngunut": "Ngunut",
                            "Pagerwojo": "Pagerwojo",
                            "Pakel": "Pakel",
                            "Rejotangan": "Rejotangan",
                            "Sendang": "Sendang",
                            "Sumbergempol": "Sumbergempol",
                            "Tanggung Gunung": "Tanggung Gunung",
                            "Boyolangu": "Boyolangu",
                            "Kedungwaru": "Kedungwaru"
                        }
                    },
                    "Kab. Trenggalek": {
                        "items": []
                    },
                    "Kota Yogyakarta": {
                        "items": {
                            "Yogyakarta": "Yogyakarta",
                            "Tegalrejo": "Tegalrejo",
                            "Danurejan": "Danurejan",
                            "Gedongtengen": "Gedongtengen",
                            "Gondokusuman": "Gondokusuman",
                            "Gondomanan": "Gondomanan",
                            "Kotagede": "Kotagede",
                            "Kraton": "Kraton",
                            "Mantrijeron": "Mantrijeron",
                            "Mergangsan": "Mergangsan",
                            "Ngampilan": "Ngampilan",
                            "Pakualaman": "Pakualaman",
                            "Umbulharjo": "Umbulharjo",
                            "Wirobrajan": "Wirobrajan"
                        }
                    },
                    "Kab. Bantul": {
                        "items": {
                            "Bantul": "Bantul",
                            "Bambanglipuro": "Bambanglipuro",
                            "Dlingo": "Dlingo",
                            "Imogiri": "Imogiri",
                            "Jetis": "Jetis",
                            "Kretek": "Kretek",
                            "Pajangan": "Pajangan",
                            "Pandak": "Pandak",
                            "Piyungan": "Piyungan",
                            "Pleret": "Pleret",
                            "Pundong": "Pundong",
                            "Sanden": "Sanden",
                            "Sedayu": "Sedayu",
                            "Srandakan": "Srandakan",
                            "Banguntapan": "Banguntapan",
                            "Kasihan": "Kasihan",
                            "Sewon": "Sewon"
                        }
                    },
                    "Kab. Sleman": {
                        "items": {
                            "Sleman": "Sleman",
                            "Berbah": "Berbah",
                            "Cangkringan": "Cangkringan",
                            "Godean": "Godean",
                            "Kalasan": "Kalasan",
                            "Minggir": "Minggir",
                            "Moyudan": "Moyudan",
                            "Ngaglik": "Ngaglik",
                            "Ngemplak": "Ngemplak",
                            "Pakem": "Pakem",
                            "Prambanan": "Prambanan",
                            "Seyegan": "Seyegan",
                            "Tempel": "Tempel",
                            "Turi": "Turi",
                            "Gamping": "Gamping",
                            "Mlati": "Mlati"
                        }
                    },
                    "Kab. Kulon Progo": {
                        "items": []
                    },
                    "Kab. Gunungkidul": {
                        "items": []
                    },
                    "Kota Denpasar": {
                        "items": {
                            "Denpasar": "Denpasar",
                            "Denpasar Barat": "Denpasar Barat",
                            "Denpasar Selatan": "Denpasar Selatan",
                            "Denpasar Timur": "Denpasar Timur"
                        }
                    },
                    "Kab. Karangasem": {
                        "items": []
                    },
                    "Kab. Bangli": {
                        "items": []
                    },
                    "Kab. Gianyar": {
                        "items": {
                            "Gianyar": "Gianyar",
                            "Sukawati": "Sukawati",
                            "Ubud": "Ubud"
                        }
                    },
                    "Kab. Jembrana": {
                        "items": []
                    },
                    "Kab. Klungkung": {
                        "items": {
                            "Semarapura": "Semarapura",
                            "Dawan": "Dawan",
                            "Klungkung": "Klungkung",
                            "Banjarangkan": "Banjarangkan"
                        }
                    },
                    "Kab. Buleleng": {
                        "items": {
                            "Singaraja": "Singaraja",
                            "Busung Biu": "Busung Biu",
                            "Gerokgak": "Gerokgak",
                            "Seririt": "Seririt",
                            "Sukasada": "Sukasada",
                            "Buleleng": "Buleleng"
                        }
                    },
                    "Kab. Tabanan": {
                        "items": {
                            "Tabanan": "Tabanan",
                            "Baturiti": "Baturiti",
                            "Kediri": "Kediri",
                            "Kerambitan": "Kerambitan",
                            "Marga": "Marga",
                            "Pupuan": "Pupuan",
                            "Penebel": "Penebel",
                            "Selemadeg": "Selemadeg",
                            "Selemadeg Barat": "Selemadeg Barat",
                            "Selemadeg Timur": "Selemadeg Timur"
                        }
                    },
                    "Kab. Badung": {
                        "items": []
                    },
                    "Kota Mataram": {
                        "items": []
                    },
                    "Kota Bima": {
                        "items": {
                            "Bima": "Bima",
                            "Asakota": "Asakota",
                            "Rasanae Barat": "Rasanae Barat",
                            "Rasanae Timur": "Rasanae Timur",
                            "Raba": "Raba",
                            "Mpunda": "Mpunda"
                        }
                    },
                    "Kab. Dompu": {
                        "items": []
                    },
                    "Kab. Lombok Tengah": {
                        "items": []
                    },
                    "Kab. Lombok Timur": {
                        "items": {
                            "Selong": "Selong",
                            "Aikmel": "Aikmel",
                            "Keruak": "Keruak",
                            "Mas Bagik": "Mas Bagik",
                            "Pringgabaya": "Pringgabaya",
                            "Sakra": "Sakra",
                            "Sikur": "Sikur",
                            "Sukamulia": "Sukamulia",
                            "Terara": "Terara",
                            "Jerowaru": "Jerowaru",
                            "Montong Gading": "Montong Gading",
                            "Pringgasela": "Pringgasela",
                            "Sakra Barat": "Sakra Barat",
                            "Sakra Timur": "Sakra Timur",
                            "Suela": "Suela",
                            "Suralaga": "Suralaga",
                            "Wanasaba": "Wanasaba"
                        }
                    },
                    "Kab. Sumbawa": {
                        "items": {
                            "Sumbawa Besar / Barat": "Sumbawa Besar / Barat",
                            "Labuhan Badas": "Labuhan Badas",
                            "Sumbawa": "Sumbawa"
                        }
                    },
                    "Kab. Lombok Barat": {
                        "items": []
                    },
                    "Kab. Lombok Utara": {
                        "items": []
                    },
                    "Kab. Sumbawa Barat": {
                        "items": []
                    },
                    "Kab. Bima": {
                        "items": []
                    },
                    "Kota Kupang": {
                        "items": []
                    },
                    "Kab. Belu": {
                        "items": []
                    },
                    "Kab. Alor": {
                        "items": []
                    },
                    "Kab. Timor Tengah Utara": {
                        "items": []
                    },
                    "Kab. Flores Timur": {
                        "items": []
                    },
                    "Kab. Sikka": {
                        "items": []
                    },
                    "Kab. Manggarai": {
                        "items": []
                    },
                    "Kab. Timor Tengah Selatan": {
                        "items": []
                    },
                    "Kab. Ende": {
                        "items": []
                    },
                    "Kab. Ngada": {
                        "items": []
                    },
                    "Kab. Sumba Timur": {
                        "items": []
                    },
                    "Kab. Sumba Barat": {
                        "items": []
                    },
                    "Kab. Manggarai Barat": {
                        "items": []
                    },
                    "Kab. Rote Ndao": {
                        "items": []
                    },
                    "Kab. Sumba Barat Daya": {
                        "items": []
                    },
                    "Kab. Lembata": {
                        "items": []
                    },
                    "Kota Pontianak": {
                        "items": {
                            "Pontianak": "Pontianak",
                            "Pontianak Barat": "Pontianak Barat",
                            "Pontianak Kota": "Pontianak Kota",
                            "Pontianak Selatan": "Pontianak Selatan",
                            "Pontianak Tenggara": "Pontianak Tenggara",
                            "Pontianak Timur/ Jungkat": "Pontianak Timur/ Jungkat",
                            "Pontianak Utara": "Pontianak Utara"
                        }
                    },
                    "Kab. Ketapang": {
                        "items": []
                    },
                    "Kab. Sanggau": {
                        "items": []
                    },
                    "Kota Singkawang": {
                        "items": []
                    },
                    "Kab. Sintang": {
                        "items": []
                    },
                    "Kab. Kapuas Hulu": {
                        "items": []
                    },
                    "Kab. Bengkayang": {
                        "items": []
                    },
                    "Kab. Landak": {
                        "items": []
                    },
                    "Kab. Melawi": {
                        "items": []
                    },
                    "Kab. Mempawah": {
                        "items": []
                    },
                    "Kab. Sambas": {
                        "items": {
                            "Sambas": "Sambas",
                            "Pemangkat": "Pemangkat",
                            "Sebawi": "Sebawi",
                            "Subah": "Subah"
                        }
                    },
                    "Kab. Kubu Raya": {
                        "items": []
                    },
                    "Kab. Barito Utara": {
                        "items": []
                    },
                    "Kab. Barito Timur": {
                        "items": []
                    },
                    "Kab. Murung Raya": {
                        "items": []
                    },
                    "Kab. Barito Selatan": {
                        "items": []
                    },
                    "Kota Palangka Raya": {
                        "items": []
                    },
                    "Kab. Katingan": {
                        "items": []
                    },
                    "Kab. Kapuas": {
                        "items": []
                    },
                    "Kab. Gunung Mas": {
                        "items": []
                    },
                    "Kab. Kotawaringin Barat": {
                        "items": []
                    },
                    "Kab. Kotawaringin Timur": {
                        "items": []
                    },
                    "Kab. Pulang Pisau": {
                        "items": []
                    },
                    "Kota Balikpapan": {
                        "items": []
                    },
                    "Kab. Paser": {
                        "items": []
                    },
                    "Kab. Penajam Paser Utara": {
                        "items": {
                            "Penajam": "Penajam",
                            "Waru": "Waru",
                            "Babulu": "Babulu"
                        }
                    },
                    "Kota Bontang": {
                        "items": {
                            "Bontang": "Bontang",
                            "Bontang Barat": "Bontang Barat",
                            "Bontang Selatan": "Bontang Selatan",
                            "Bontang Utara": "Bontang Utara"
                        }
                    },
                    "Kab. Kutai Timur": {
                        "items": []
                    },
                    "Kota Samarinda": {
                        "items": {
                            "Samarinda": "Samarinda",
                            "Samarinda Seberang": "Samarinda Seberang",
                            "Palaran": "Palaran",
                            "Samarinda Ilir": "Samarinda Ilir",
                            "Samarinda Ulu": "Samarinda Ulu",
                            "Samarinda Utara": "Samarinda Utara",
                            "Sungai Kunjang": "Sungai Kunjang"
                        }
                    },
                    "Kab. Kutai Kartanegara": {
                        "items": []
                    },
                    "Kab. Kutai Barat": {
                        "items": []
                    },
                    "Kota Banjarmasin": {
                        "items": {
                            "Banjarmasin": "Banjarmasin",
                            "Banjarmasin Barat": "Banjarmasin Barat",
                            "Banjarmasin Selatan": "Banjarmasin Selatan",
                            "Banjarmasin Tengah": "Banjarmasin Tengah",
                            "Banjarmasin Timur": "Banjarmasin Timur",
                            "Banjarmasin Utara": "Banjarmasin Utara"
                        }
                    },
                    "Kab. Hulu Sungai Tengah": {
                        "items": []
                    },
                    "Kab. Hulu Sungai Selatan": {
                        "items": []
                    },
                    "Kab. Kotabaru": {
                        "items": []
                    },
                    "Kab. Tabalong": {
                        "items": []
                    },
                    "Kota Banjarbaru": {
                        "items": {
                            "BanjarBaru": "BanjarBaru",
                            "Cempaka": "Cempaka",
                            "Landasan Ulin": "Landasan Ulin"
                        }
                    },
                    "Kab. Hulu Sungai Utara": {
                        "items": []
                    },
                    "Kab. Barito Kuala": {
                        "items": []
                    },
                    "Kab. Banjar": {
                        "items": []
                    },
                    "Kab. Tanah Laut": {
                        "items": {
                            "Pelaihari": "Pelaihari",
                            "Bati-Bati": "Bati-Bati",
                            "Jorong": "Jorong",
                            "Kintap": "Kintap",
                            "Takisung": "Takisung",
                            "Batu Ampar": "Batu Ampar"
                        }
                    },
                    "Kab. Tapin": {
                        "items": {
                            "Bakarangan": "Bakarangan",
                            "Bungur": "Bungur",
                            "Candi Laras Utara": "Candi Laras Utara",
                            "Candi Laras Selatan": "Candi Laras Selatan",
                            "Hatungun": "Hatungun",
                            "Lokpaikat": "Lokpaikat",
                            "Piani": "Piani",
                            "Salam Babaris": "Salam Babaris",
                            "Tapin Tengah": "Tapin Tengah",
                            "Tapin Selatan": "Tapin Selatan",
                            "Tapin Utara": "Tapin Utara"
                        }
                    },
                    "Kab. Balangan": {
                        "items": []
                    },
                    "Kab. Tanah Bumbu": {
                        "items": []
                    },
                    "Kota Tarakan": {
                        "items": {
                            "Tarakan": "Tarakan",
                            "Tarakan Barat": "Tarakan Barat",
                            "Tarakan Tengah": "Tarakan Tengah",
                            "Tarakan Timur": "Tarakan Timur",
                            "Tarakan Utara": "Tarakan Utara"
                        }
                    },
                    "Kab. Bulungan": {
                        "items": []
                    },
                    "Kab. Malinau": {
                        "items": []
                    },
                    "Kab. Nunukan": {
                        "items": []
                    },
                    "Kab. Tana Tidung": {
                        "items": []
                    },
                    "Kota Manado": {
                        "items": []
                    },
                    "Kab. Bolaang Mongondow": {
                        "items": []
                    },
                    "Kab. Kepulauan Sangihe": {
                        "items": []
                    },
                    "Kab. Minahasa": {
                        "items": []
                    },
                    "Kota Bitung": {
                        "items": []
                    },
                    "Kab. Kepulauan Talaud": {
                        "items": []
                    },
                    "Kab. Minahasa Utara": {
                        "items": []
                    },
                    "Kab. Minahasa Selatan": {
                        "items": []
                    },
                    "Kab. Minahasa Tenggara": {
                        "items": []
                    },
                    "Kota Tomohon": {
                        "items": []
                    },
                    "Kota Palu": {
                        "items": []
                    },
                    "Kab. Banggai": {
                        "items": {
                            "Luwuk": "Luwuk",
                            "Balantak": "Balantak",
                            "Batui": "Batui",
                            "Bunta": "Bunta",
                            "Kintom": "Kintom",
                            "Lamala": "Lamala",
                            "Pagimana": "Pagimana",
                            "Boalemo": "Boalemo",
                            "Toili": "Toili"
                        }
                    },
                    "Kab. Poso": {
                        "items": []
                    },
                    "Kab. Toli-Toli": {
                        "items": []
                    },
                    "Kab. Buol": {
                        "items": []
                    },
                    "Kab. Donggala": {
                        "items": []
                    },
                    "Kab. Morowali": {
                        "items": []
                    },
                    "Kab. Parigi Moutong": {
                        "items": {
                            "Parigi": "Parigi",
                            "Moutong": "Moutong",
                            "Sausu": "Sausu",
                            "Torue": "Torue"
                        }
                    },
                    "Kab. Tojo Una-Una": {
                        "items": []
                    },
                    "Kota Kendari": {
                        "items": {
                            "Kendari": "Kendari",
                            "Abeli": "Abeli",
                            "Baruga": "Baruga",
                            "Barat": "Barat",
                            "Mandonga": "Mandonga",
                            "Poasia": "Poasia"
                        }
                    },
                    "Kota Bau-Bau": {
                        "items": []
                    },
                    "Kab. Kolaka": {
                        "items": []
                    },
                    "Kab. Muna": {
                        "items": []
                    },
                    "Kab. Konawe": {
                        "items": []
                    },
                    "Kab. Bombana": {
                        "items": []
                    },
                    "Kab. Buton Utara": {
                        "items": []
                    },
                    "Kab. Kolaka Utara": {
                        "items": []
                    },
                    "Kab. Wakatobi": {
                        "items": []
                    },
                    "Kab. Mamuju": {
                        "items": {
                            "Mamuju": "Mamuju",
                            "Kalukku": "Kalukku",
                            "Tappalang": "Tappalang",
                            "Simboro dan Kepulauan": "Simboro dan Kepulauan",
                            "Tobadak": "Tobadak",
                            "Topoyo": "Topoyo"
                        }
                    },
                    "Kab. Majene": {
                        "items": []
                    },
                    "Kab. Polewali Mandar": {
                        "items": []
                    },
                    "Kab. Mamasa": {
                        "items": []
                    },
                    "Kab. Mamuju Utara": {
                        "items": []
                    },
                    "Kota Makassar": {
                        "items": {
                            "Ujung Pandang": "Ujung Pandang",
                            "Tamalate": "Tamalate",
                            "Biring Kanaya": "Biring Kanaya",
                            "Bontoala": "Bontoala",
                            "Mamajang": "Mamajang",
                            "Manggala": "Manggala",
                            "Mariso": "Mariso",
                            "Panakkukang": "Panakkukang",
                            "Rappocini": "Rappocini",
                            "Tallo": "Tallo",
                            "Tamalanrea": "Tamalanrea",
                            "Ujung Tanah": "Ujung Tanah",
                            "Wajo": "Wajo"
                        }
                    },
                    "Kab. Bantaeng": {
                        "items": []
                    },
                    "Kab. Barru": {
                        "items": []
                    },
                    "Kab. Bulukumba": {
                        "items": []
                    },
                    "Kab. Enrekang": {
                        "items": {
                            "Enrekang": "Enrekang",
                            "Alla": "Alla",
                            "Anggeraja": "Anggeraja",
                            "Baraka": "Baraka",
                            "Maiwa": "Maiwa",
                            "Maiwa Atas": "Maiwa Atas"
                        }
                    },
                    "Kab. Jeneponto": {
                        "items": []
                    },
                    "Kab. Tana Toraja": {
                        "items": []
                    },
                    "Kab. Toraja Utara": {
                        "items": []
                    },
                    "Kab. Maros": {
                        "items": []
                    },
                    "Kota Palopo": {
                        "items": []
                    },
                    "Kab. Pinrang": {
                        "items": []
                    },
                    "Kab. Sidenreng Rappang": {
                        "items": {
                            "Sidenreng": "Sidenreng",
                            "Baranti": "Baranti",
                            "Duapitue": "Duapitue",
                            "Panca Rijang": "Panca Rijang",
                            "Panca Lautang": "Panca Lautang",
                            "Tellulimpo E": "Tellulimpo E",
                            "Watang Pulu": "Watang Pulu",
                            "Kulo": "Kulo",
                            "Maritengngae": "Maritengngae",
                            "Pitu Riase": "Pitu Riase",
                            "Pitu Riawa": "Pitu Riawa",
                            "Sidrap": "Sidrap"
                        }
                    },
                    "Kab. Wajo": {
                        "items": {
                            "Sengkang": "Sengkang",
                            "Belawa": "Belawa",
                            "Majauleng": "Majauleng",
                            "Maniang Pajo": "Maniang Pajo",
                            "Pamanna": "Pamanna",
                            "Pitumpanua": "Pitumpanua",
                            "Sabbang Paru": "Sabbang Paru",
                            "Sajoanging": "Sajoanging",
                            "Takkalalla": "Takkalalla",
                            "Bola": "Bola",
                            "Gilireng": "Gilireng",
                            "Keera": "Keera",
                            "Penrang": "Penrang",
                            "Tempe": "Tempe"
                        }
                    },
                    "Kab. Sinjai": {
                        "items": []
                    },
                    "Kab. Gowa": {
                        "items": {
                            "Sungguminasa": "Sungguminasa",
                            "Bajeng": "Bajeng",
                            "Bontomarannu": "Bontomarannu",
                            "Pallangga": "Pallangga",
                            "Parangloe": "Parangloe",
                            "Tompobulu": "Tompobulu",
                            "Barombong": "Barombong",
                            "Somba Opu": "Somba Opu"
                        }
                    },
                    "Kab. Takalar": {
                        "items": []
                    },
                    "Kab. Bone": {
                        "items": {
                            "Watampone": "Watampone",
                            "Ajangale": "Ajangale",
                            "Barebbo": "Barebbo",
                            "Cina": "Cina",
                            "Kahu": "Kahu",
                            "Lappariaja": "Lappariaja",
                            "Mare": "Mare",
                            "Salomekko": "Salomekko",
                            "Sibulue": "Sibulue",
                            "Tellu Siattinge": "Tellu Siattinge",
                            "Tonra": "Tonra",
                            "Ulaweng": "Ulaweng",
                            "Amali": "Amali",
                            "Awangpone": "Awangpone",
                            "Bengo": "Bengo",
                            "Palakka": "Palakka",
                            "Tanete Riattang": "Tanete Riattang",
                            "Tanete Riattang Barat": "Tanete Riattang Barat",
                            "Tanete Riattang Timur": "Tanete Riattang Timur"
                        }
                    },
                    "Kab. Soppeng": {
                        "items": []
                    },
                    "Kab. Kepulauan Selayar": {
                        "items": []
                    },
                    "Kab. Pangkajene dan Kepulauan": {
                        "items": []
                    },
                    "Kota Parepare": {
                        "items": {
                            "Pare-Pare": "Pare-Pare",
                            "Bacukiki": "Bacukiki",
                            "Soreang": "Soreang",
                            "Ujung": "Ujung"
                        }
                    },
                    "Kab. Luwu Utara": {
                        "items": []
                    },
                    "Kab. Luwu Timur": {
                        "items": {
                            "Malili / Soroako": "Malili / Soroako",
                            "Angkona": "Angkona",
                            "Burau": "Burau",
                            "Mangkutana": "Mangkutana",
                            "Nuha": "Nuha",
                            "Tomoni": "Tomoni",
                            "Towuti": "Towuti",
                            "Wotu": "Wotu"
                        }
                    },
                    "Kota Gorontalo": {
                        "items": []
                    },
                    "Kab. Gorontalo": {
                        "items": {
                            "Limboto": "Limboto",
                            "Tibawa": "Tibawa",
                            "Anggrek": "Anggrek",
                            "Limboto Barat": "Limboto Barat",
                            "Telaga": "Telaga"
                        }
                    },
                    "Kab. Boalemo": {
                        "items": []
                    },
                    "Kab. Bone Bolango": {
                        "items": []
                    },
                    "Kab. Gorontalo Utara": {
                        "items": []
                    },
                    "Kab. Pohuwato": {
                        "items": []
                    },
                    "Kota Ambon": {
                        "items": []
                    },
                    "Kab. Maluku Tengah": {
                        "items": []
                    },
                    "Kab. Maluku Tenggara": {
                        "items": []
                    },
                    "Kab. Buru": {
                        "items": []
                    },
                    "Kab. Seram Bagian Barat": {
                        "items": []
                    },
                    "Kab. Maluku Tenggara Barat": {
                        "items": []
                    },
                    "Kota Ternate": {
                        "items": {
                            "Ternate": "Ternate",
                            "Ternate Selatan": "Ternate Selatan",
                            "Ternate Utara": "Ternate Utara"
                        }
                    },
                    "Kab. Halmahera Utara": {
                        "items": []
                    },
                    "Kota Tidore Kepulauan": {
                        "items": []
                    },
                    "Kota Jayapura": {
                        "items": {
                            "Jayapura": "Jayapura",
                            "Abepura": "Abepura",
                            "Jayapura Selatan": "Jayapura Selatan",
                            "Jayapura Utara": "Jayapura Utara",
                            "Muara Tam": "Muara Tam"
                        }
                    },
                    "Kab. Jayapura": {
                        "items": []
                    },
                    "Kab. Biak Numfor": {
                        "items": {
                            "Biak Kota": "Biak Kota",
                            "Biak timur": "Biak timur",
                            "Samofa": "Samofa",
                            "Yendidori": "Yendidori"
                        }
                    },
                    "Kab. Merauke": {
                        "items": {
                            "Merauke": "Merauke"
                        }
                    },
                    "Kab. Jayawijaya": {
                        "items": {
                            "Wamena": "Wamena",
                            "Asologaima": "Asologaima",
                            "Kurulu": "Kurulu",
                            "Hubikosi": "Hubikosi"
                        }
                    },
                    "Kab. Nabire": {
                        "items": []
                    },
                    "Kab. Paniai": {
                        "items": []
                    },
                    "Kab. Puncak Jaya": {
                        "items": []
                    },
                    "Kab. Intan Jaya": {
                        "items": []
                    },
                    "Kab. Deiyai": {
                        "items": []
                    },
                    "Kab. Mimika": {
                        "items": {
                            "Agimuga": "Agimuga",
                            "Jita": "Jita",
                            "Kuala Kencana": "Kuala Kencana",
                            "Mimika Barat": "Mimika Barat",
                            "Mimika Baru": "Mimika Baru",
                            "Mimika Timur": "Mimika Timur",
                            "Mimika Timur Jauh": "Mimika Timur Jauh",
                            "Tembaga Pura": "Tembaga Pura"
                        }
                    },
                    "Kab. Sorong": {
                        "items": []
                    },
                    "Kota Sorong": {
                        "items": []
                    },
                    "Kab. Fakfak": {
                        "items": []
                    },
                    "Kab. Kaimana": {
                        "items": []
                    },
                    "Kab. Raja Ampat": {
                        "items": []
                    },
                    "Kab. Sorong Selatan": {
                        "items": []
                    },
                    "Kab. Teluk Bintuni": {
                        "items": []
                    },
                    "Kab. Manokwari": {
                        "items": []
                    }
                }');
	}
	/**
	 * @return array
	 */
	public static function shipping_get_kota() {
		$states = WC()->countries->get_allowed_country_states();
		$result = array();
		foreach ( $states['ID'] as $key => $state ) {
			// Check if we need to recalculate shipping for this city.
			$city_hash = 'wc_ship_tiki_kota_' . md5( $state ) . WC_Cache_Helper::get_transient_version( 'tiki_shipping_kota' );
			if ( false === ( $stored_city = get_transient( $city_hash ) ) ) {
				$result[ $key ] = array(
					'items' => array(),
				);
				$args           = array(
					'action'  => 'get_kota',
					'keyword' => $state,
				);
				$response       = WC_TIKI()->api->remote_get( 'kota', $args );
				// store transient
				if ( ! empty( $response['result'] ) ) {
					WC_TIKI()->set_transient( $city_hash, $response['result'] );
				}
			} else {
				$response['result'] = $stored_city;
			}
			foreach ( $response['result'] as $item ) {
				$result[ $key ]['items'][ $item ] = $item;
			}
		}

		return $result;
	}


	public static function shipping_get_kecamatan() {
		$kotas = self::shipping_get_kota();
		// Check if we need to recalculate shipping for this kecamatan.
		$result = array();
		foreach ( $kotas as $provinsi => $items ) {
			foreach ( $items['items'] as $kota ) {
				$result[ $kota ] = array( 'items' => array() );
				$kec_hash        = 'wc_ship_tiki_kec_' . md5( $kota ) . WC_Cache_Helper::get_transient_version( 'tiki_shipping_kec' );
				if ( false === ( $stored_kec = get_transient( $kec_hash ) ) ) {
					$args     = array(
						'action'  => 'get_kecamatan',
						'keyword' => $provinsi . '|' . $kota,
					);
					$response = WC_TIKI()->api->remote_get( 'kota', $args );
					// store transient
					if ( ! empty( $response['result'] ) ) {
						WC_TIKI()->set_transient( $kec_hash, $response['result'] );
					}
				} else {
					$response['result'] = $stored_kec;
				}
				foreach ( $response['result'] as $item ) {
					$result[ $kota ]['items'][ $item ] = $item;
				}
			}
		}

		return $result;
	}
}

new APPMAKER_WC_tiki();
