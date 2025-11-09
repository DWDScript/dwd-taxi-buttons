<?php
/*
Plugin Name: DWD Taxi Rezervasyon Butonu
Description: Elementor ile tam uyumlu gelişmiş "Transfer Card" ve "Rezervasyon Butonu" bileşenleri. Güzergah, fiyat, süre, yolcu sayısı ve tüm görsel stiller Elementor üzerinden dinamik olarak kontrol edilir. Veriler doğrudan VIPRez sistemindeki sabit_fiyatlar tablosundan çekilir ve modal pencerede rezervasyon formu açılır.
Version: 1.0.5
Author: Detail Web Design
*/

if (! defined( 'ABSPATH' ) ) exit;

/**
 * GLOBAL FOOTER MODAL
 */
add_action('wp_footer', function(){
    ?>
    <div id="dwd-modal-global" style="display:none;align-items:center;justify-content:center;">
      <div id="dwd-modal-overlay" aria-hidden="true"></div>
      <div id="dwd-modal-wrap" role="dialog" aria-modal="true">
        <button id="dwd-modal-close" aria-label="<?php esc_attr_e('Kapat','dwd-transfer'); ?>">&times;</button>
        <iframe id="dwd-modal-iframe" src="" frameborder="0" title="<?php esc_attr_e('Rezervasyon','dwd-transfer'); ?>"></iframe>
      </div>
    </div>
    <?php
});

/**
 * SCRIPTS & STYLES
 */
add_action('wp_enqueue_scripts', function(){
    if (!wp_style_is('dwd-transfer-style-v2')) {
        wp_register_style('dwd-transfer-style-v2', false);
        wp_enqueue_style('dwd-transfer-style-v2');
        wp_add_inline_style('dwd-transfer-style-v2', '
/* ============================================
   CARD WRAPPER VE İÇ YAPI
   ============================================ */
.dwd-card-wrapper {
  display: inline-block;
  width: 100%;
  max-width: 400px;
  box-sizing: border-box;
}

.dwd-card-inner {
  position: relative;
  overflow: hidden;
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-sizing: border-box;
}

/* CARD ARKA PLAN GÖRSELİ + OVERLAY */
.dwd-card-inner::before,
.dwd-card-inner::after {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  z-index: 0;
}
.dwd-card-inner::before {
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}
.dwd-card-inner::after {
  background: transparent;
}
.dwd-card-inner > * {
  position: relative;
  z-index: 1;
}

/* ============================================
   CARD İÇERİK ALANLARI
   ============================================ */
.dwd-card-route {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 8px;
}
.dwd-card-route-text {
  font-size: 18px;
  font-weight: 600;
  color: #333;
}
.dwd-card-route-icon {
  font-size: 24px;
  margin: 0 8px;
  line-height: 1;
}

.dwd-card-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 24px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.dwd-card-stat-item {
  display: flex;
  align-items: center;
  gap: 8px;
}
.dwd-card-stat-icon {
  font-size: 20px;
  line-height: 1;
}
.dwd-card-stat-text {
  font-size: 16px;
  font-weight: 500;
  color: #666;
}

/* ============================================
   CARD PRICE SECTION - HIZALAMA DÜZELTİLDİ
   ============================================ */
.dwd-card-price-section {
  display: block;
  margin-bottom: 20px;
  text-align: center;
}

.dwd-card-price-box {
  display: inline-flex;
  align-items: baseline;
  background-color: transparent;
  padding: 12px 20px;
  border-radius: 6px;
  box-sizing: border-box;
}

.dwd-card-price-label {
  font-size: 14px;
  color: #999;
  text-transform: uppercase;
  font-weight: 600;
  margin-right: 8px;
}
.dwd-card-price-value {
  font-size: 36px;
  font-weight: 700;
  color: #4CAF50;
  line-height: 1;
}
.dwd-card-price-currency {
  font-size: 24px;
  margin-left: 4px;
}

/* ============================================
   CARD BUTON
   ============================================ */
.dwd-card-button {
  display: inline-block;
  padding: 14px 28px;
  background: #4CAF50;
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  width: 100%;
  box-sizing: border-box;
}

.dwd-card-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* ============================================
   RIBBON
   ============================================ */
.dwd-card-ribbon {
  position: absolute;
  padding: 6px 14px;
  font-weight: 700;
  font-size: 13px;
  border-radius: 3px;
  z-index: 5;
  text-align: center;
  line-height: 1.4;
}

/* Ribbon Pozisyonları - Basit */
.dwd-card-ribbon.top-left { top: 10px; left: 10px; }
.dwd-card-ribbon.top-right { top: 10px; right: 10px; }
.dwd-card-ribbon.bottom-left { bottom: 10px; left: 10px; }
.dwd-card-ribbon.bottom-right { bottom: 10px; right: 10px; }

/* Ribbon - Düz Bant (Full Width) */
.dwd-card-ribbon.straight {
  left: 0;
  right: 0;
  width: 100%;
  padding: 10px 20px;
  border-radius: 0;
}

.dwd-card-ribbon.straight.top-full { top: 0; }
.dwd-card-ribbon.straight.bottom-full { bottom: 0; }

/* Ribbon - Çapraz (Diagonal) */
.dwd-card-ribbon.diagonal {
  display: flex;
  align-items: center;
  justify-content: center;
  transform-origin: center;
  min-width: 160px;
  white-space: nowrap;
}

.dwd-card-ribbon.diagonal.top-right {
  top: 20px;
  right: -40px;
  transform: rotate(45deg);
}

.dwd-card-ribbon.diagonal.top-left {
  top: 20px;
  left: -40px;
  transform: rotate(-45deg);
}

.dwd-card-ribbon.diagonal.bottom-right {
  bottom: 20px;
  right: -40px;
  transform: rotate(-45deg);
}

.dwd-card-ribbon.diagonal.bottom-left {
  bottom: 20px;
  left: -40px;
  transform: rotate(45deg);
}

/* ============================================
   BUTTON WRAPPER
   ============================================ */
.dwd-button-wrapper {
  display: inline-flex;
  align-items: stretch;
  cursor: pointer;
  text-decoration: none;
  overflow: hidden;
  border-radius: 8px;
  width: 100%;
  max-width: 100% !important;
  box-sizing: border-box;
  transition: all 0.3s ease;
}

.dwd-button-wrapper:hover {
  transform: translateX(4px);
}

.dwd-button-content {
  display: flex;
  flex-direction: column;
  padding: 16px 20px;
  flex: 1;
  gap: 8px;
}

.dwd-button-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 500;
}

.dwd-button-row-icon { 
  font-size: 16px;
  line-height: 1;
}

.dwd-button-row-text { 
  font-size: 16px;
  line-height: 1.4;
}

.dwd-button-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 24px;
  font-size: 32px;
  line-height: 1;
}

/* ============================================
   LINK PREVIEW (SADECE EDITÖRDE)
   ============================================ */
.dwd-link-preview {
  font-family: monospace;
  background: #f5f5f5;
  padding: 8px;
  border-radius: 4px;
  word-break: break-all;
  border: 1px solid #e1e1e1;
  margin-top: 12px;
  font-size: 11px;
}
body:not(.elementor-editor-active) .dwd-link-preview {
  display: none !important;
}

/* ============================================
   MODAL SİSTEMİ
   ============================================ */
#dwd-modal-global {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  z-index: 999999;
  display: none;
  align-items: center;
  justify-content: center;
}
#dwd-modal-overlay {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  cursor: pointer;
}
#dwd-modal-wrap {
  position: relative;
  width: 90%; height: 90%;
  max-width: 1200px; max-height: 100%;
  background: #fff;
  border-radius: 6px;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0,0,0,.25);
  z-index: 100000;
}
#dwd-modal-close {
  position: absolute;
  top: 12px; right: 16px;
  z-index: 20;
  background: #d00;
  color: #fff;
  border: none;
  padding: 8px 12px;
  font-size: 20px;
  border-radius: 4px;
  cursor: pointer;
  line-height: 1;
}
#dwd-modal-iframe {
  width: 100%; height: 100%;
  border: 0;
  display: block;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 800px) {
  #dwd-modal-wrap {
    width: 100%; height: 100%;
    border-radius: 0;
  }
}

@media (max-width: 768px) {
  .dwd-card-wrapper {
    max-width: 100%;
  }
  
  .dwd-card-route {
    flex-direction: column;
    gap: 4px;
  }
  
  .dwd-card-stats {
    flex-direction: column;
    gap: 12px;
  }
  
  .dwd-button-wrapper {
    flex-direction: column;
  }
  
  .dwd-button-arrow {
    padding: 12px;
    font-size: 24px;
  }
  
  .dwd-card-ribbon.diagonal {
    font-size: 11px;
    min-width: 120px;
  }
}
        ');
    }

    if (!wp_script_is('dwd-button-plugin')) {
        wp_enqueue_script('jquery');
        wp_register_script('dwd-button-plugin', false, ['jquery'], null, true);
        wp_enqueue_script('dwd-button-plugin');

        wp_localize_script('dwd-button-plugin', 'dwdAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dwd_route_nonce')
        ]);

        wp_add_inline_script('dwd-button-plugin', '
(function($){
    "use strict";
    
    console.log("🚀 DWD COMPLETE VERSION v1.0.5");

    var checkElementor = setInterval(function(){
        if(typeof elementor !== "undefined" && elementor.hooks && elementor.getPanelView){
            clearInterval(checkElementor);
            console.log("✅ Elementor READY");
            
            elementor.hooks.addAction("panel/open_editor/widget/dwd-button-plugin", function(panel, model, view){
                console.log("📝 PANEL OPENED - Model ID:", model.cid);
                
                setTimeout(function(){
                    var panelView = elementor.getPanelView();
                    if(!panelView || !panelView.$el) return;
                    
                    var $panel = panelView.$el;
                    var $routeSelect = $panel.find("select[data-setting=route_id]");
                    
                    if($routeSelect.length){
                        $routeSelect.off("change.dwd").on("change.dwd", function(){
                            var rid = $(this).val();
                            if(rid && model){
                                $.ajax({
                                    url: dwdAjax.ajaxurl,
                                    type: "POST",
                                    data: {action: "dwd_get_route_data", route_id: rid, nonce: dwdAjax.nonce},
                                    success: function(res){
                                        if(res.success && res.data){
                                            var d = res.data;
                                            
                                            // Güzergah metinlerini güncelle
                                            model.setSetting("card_route_text_start", d.baslangic_tr);
                                            model.setSetting("card_route_text_end", d.bitis_tr);
                                            
                                            $panel.find("input[data-setting=card_route_text_start]").val(d.baslangic_tr);
                                            $panel.find("input[data-setting=card_route_text_end]").val(d.bitis_tr);
                                            
                                            // SEO alanlarını otomatik doldur
                                            var seoName = d.baslangic_tr + " → " + d.bitis_tr + " Transfer";
                                            var seoDesc = d.baslangic_tr + " dan " + d.bitis_tr + " a transfer hizmeti. Mesafe: " + d.km + " km, Süre: " + Math.floor(d.sure/60) + " saat " + (d.sure%60) + " dakika. Fiyat: €" + d.gunduz_1_3_kisi + " dan başlayan fiyatlarla.";
                                            
                                            model.setSetting("seo_product_name", seoName);
                                            model.setSetting("seo_description", seoDesc);
                                            
                                            $panel.find("input[data-setting=seo_product_name]").val(seoName);
                                            $panel.find("textarea[data-setting=seo_description]").val(seoDesc);
                                            
                                            console.log("✅ GÜNCELLENDI:", d.baslangic_tr, "→", d.bitis_tr);
                                        }
                                    }
                                });
                            }
                        });
                        var initialValue = $routeSelect.val();
                        if(initialValue) $routeSelect.trigger("change.dwd");
                    }
                }, 800);
            });
        }
    }, 100);

    // Modal sistem
    $(document).on("click", ".dwd-card-button, .dwd-button-wrapper", function(e){
        e.preventDefault();
        var link = $(this).attr("href") || $(this).data("modal-link");
        if(!link) return;
        
        var $m = $("#dwd-modal-global");
        if(!$m.length) return window.location.href = link;
        
        $("#dwd-modal-overlay").css("background", $(this).data("modal-bg") || "rgba(0,0,0,0.6)");
        var f = $(this).data("modal-full") === "yes";
        $("#dwd-modal-wrap").css({
            width: f ? "100%" : ($(this).data("modal-width") || "90%"),
            height: f ? "100%" : ($(this).data("modal-height") || "90%"),
            borderRadius: f ? "0" : "6px"
        });
        $("#dwd-modal-iframe").attr("src", link);
        $m.fadeIn(200).css("display","flex");
    });

    $(document).on("click", "#dwd-modal-close, #dwd-modal-overlay", function(){
        $("#dwd-modal-global").fadeOut(200);
        $("#dwd-modal-iframe").attr("src", "");
    });

})(jQuery);
');
    }
});

/**
 * AJAX Handler
 */
add_action('wp_ajax_dwd_get_route_data', 'dwd_get_route_data_handler_fixed');
add_action('wp_ajax_nopriv_dwd_get_route_data', 'dwd_get_route_data_handler_fixed');

function dwd_get_route_data_handler_fixed() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dwd_route_nonce')) {
        error_log('DWD: Invalid nonce');
        wp_send_json_error(['message' => 'Invalid nonce']);
        return;
    }
    
    $route_id = isset($_POST['route_id']) ? absint($_POST['route_id']) : 0;
    if ($route_id <= 0) {
        error_log('DWD: Invalid route ID: ' . $route_id);
        wp_send_json_error(['message' => 'Invalid route ID']);
        return;
    }

    $config_path = ABSPATH . 'transfer/inc/config.php';
    if (!file_exists($config_path)) {
        error_log('DWD: Config file not found at: ' . $config_path);
        wp_send_json_error(['message' => 'Config file not found']);
        return;
    }

    global $db, $hostname, $username, $password, $database;
    include_once $config_path;

    if (!isset($db) || !($db instanceof PDO)) {
        if (isset($hostname, $username, $password, $database) && !empty($hostname)) {
            try {
                $db = new PDO(
                    "mysql:host={$hostname};dbname={$database};charset=utf8mb4", 
                    $username, 
                    $password, 
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                error_log('DWD: Database connection created successfully');
            } catch (Exception $e) {
                error_log('DWD: Database connection error: ' . $e->getMessage());
                wp_send_json_error(['message' => 'Database connection failed']);
                return;
            }
        } else {
            error_log('DWD: Database credentials not found');
            wp_send_json_error(['message' => 'Database credentials not found']);
            return;
        }
    }

    try {
        $stmt = $db->prepare("SELECT baslangic_tr, bitis_tr, km, sure, gunduz_1_3_kisi FROM sabit_fiyatlar WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $route_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            error_log('DWD: Route data found for ID ' . $route_id . ': ' . json_encode($data));
            wp_send_json_success($data);
        } else {
            error_log('DWD: Route not found for ID: ' . $route_id);
            wp_send_json_error(['message' => 'Route not found']);
        }
    } catch (Exception $e) {
        error_log('DWD: Database query error: ' . $e->getMessage());
        wp_send_json_error(['message' => 'Database query failed: ' . $e->getMessage()]);
    }
}

/**
 * Elementor Widget
 */
add_action('elementor/widgets/widgets_registered', function($widgets_manager){
    if (!class_exists('\Elementor\Widget_Base')) {
        return;
    }

    class DWD_Transfer_Widget_Complete extends \Elementor\Widget_Base {

        public function get_name() { return 'dwd-button-plugin'; }
        public function get_title() { return __('DWD Transfer (Complete)', 'dwd-transfer'); }
        public function get_icon() { return 'eicon-button'; }
        public function get_categories() { return ['general']; }

        private static $pdo_instance = null;

        private function connect_db() {
            if (self::$pdo_instance !== null) {
                return self::$pdo_instance;
            }

            $possible = ABSPATH . 'transfer/inc/config.php';
            if (file_exists($possible)) {
                global $hostname, $username, $password, $database, $db;
                
                if (isset($db) && $db instanceof PDO) {
                    self::$pdo_instance = $db;
                    return self::$pdo_instance;
                }
                
                include_once $possible;
                global $db;
                
                if (isset($db) && $db instanceof PDO) {
                    self::$pdo_instance = $db;
                    return self::$pdo_instance;
                }
                
                if (isset($hostname, $username, $password, $database) && !empty($hostname)) {
                    try {
                        self::$pdo_instance = new PDO(
                            "mysql:host={$hostname};dbname={$database};charset=utf8mb4", 
                            $username, 
                            $password, 
                            [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                            ]
                        );
                        return self::$pdo_instance;
                    } catch (Exception $e) {
                        error_log('DWD Transfer DB Error: ' . $e->getMessage());
                    }
                }
            }
            return null;
        }

        private function get_routes() {
            $pdo = $this->connect_db();
            $options = [];
            if ($pdo) {
                try {
                    $stmt = $pdo->query("SELECT id, baslangic_tr, bitis_tr FROM sabit_fiyatlar ORDER BY baslangic_tr ASC, bitis_tr ASC");
                    $results = $stmt->fetchAll();
                    foreach ($results as $r) {
                        $options[(int)$r['id']] = trim($r['baslangic_tr']) . ' → ' . trim($r['bitis_tr']);
                    }
                } catch (Exception $e) {
                    error_log('DWD Transfer get_routes Error: ' . $e->getMessage());
                }
            }
            if (empty($options)) {
                $options[0] = __('Veritabanında güzergah bulunamadı', 'dwd-transfer');
            }
            return $options;
        }

        protected function register_controls() {
            $routes = $this->get_routes();

            // ============================================
            // GENEL AYARLAR
            // ============================================
            $this->start_controls_section('general_section', [
                'label' => __('🎯 Genel Ayarlar', 'dwd-transfer')
            ]);

            $this->add_control('view_type', [
                'label' => __('Görünüm Türü', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'card' => 'Card (Kart Görünümü)',
                    'button' => 'Button (Buton Görünümü)'
                ],
                'default' => 'card',
            ]);

            $this->add_control('route_id', [
                'label' => __('Güzergah Seç (DB)', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $routes,
                'default' => array_key_first($routes),
            ]);

            $this->add_control('passenger', [
                'label'=>__('Yolcu Sayısı','dwd-transfer'),
                'type'=>\Elementor\Controls_Manager::NUMBER,
                'default'=>1,
                'min'=>1,
                'max'=>8,
            ]);

            $this->add_control('baggage', [
                'label'=>__('Bagaj','dwd-transfer'),
                'type'=>\Elementor\Controls_Manager::NUMBER,
                'default'=>1,
                'min'=>0,
                'max'=>8,
            ]);

            $this->add_control('transfertipi', [
                'label'=>__('Transfer Tipi','dwd-transfer'),
                'type'=>\Elementor\Controls_Manager::SELECT,
                'options'=>['0'=>'Tek Yön','1'=>'Gidiş Dönüş'],
                'default'=>'0',
            ]);

            $this->add_control('viprez_path', [
                'label' => __('VIPRez Yolu', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '/transfer/viprez.php',
            ]);
            
            $this->end_controls_section();

            // ============================================
            // CARD AYARLARI
            // ============================================
            $this->start_controls_section('card_content_section', [
                'label' => __('📋 Card İçerik', 'dwd-transfer'),
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_control('card_route_text_start', [
                'label' => __('Başlangıç Konumu', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Abholort',
            ]);

            $this->add_control('card_route_icon', [
                'label' => __('Orta Icon HTML', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '<i class="fa-solid fa-arrow-right"></i>',
                'rows' => 2,
            ]);

            $this->add_control('card_route_text_end', [
                'label' => __('Varış Konumu', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Zielort',
            ]);

            $this->add_control('card_km_icon', [
                'label' => __('KM Icon HTML', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '<i class="fa-solid fa-route"></i>',
                'rows' => 2,
            ]);

            $this->add_control('card_time_icon', [
                'label' => __('Süre Icon HTML', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '<i class="fa-solid fa-clock"></i>',
                'rows' => 2,
            ]);

            $this->add_control('card_price_label', [
                'label' => __('Fiyat Etiketi (AB yerine)', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'AB',
            ]);

            $this->add_control('card_currency', [
                'label' => __('Döviz Cinsi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '€',
            ]);

            $this->add_control('card_button_text', [
                'label' => __('Buton Metni', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Jetzt buchen',
            ]);

            // SEO Alanları
            $this->add_control('seo_heading', [
                'label' => __('SEO Bilgileri', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]);

            $this->add_control('seo_product_name', [
                'label' => __('SEO Ürün Adı (Otomatik)', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => 'Otomatik doldurulacak...',
                'description' => __('Güzergah seçildiğinde otomatik doldurulur.', 'dwd-transfer'),
            ]);

            $this->add_control('seo_description', [
                'label' => __('SEO Açıklaması (Otomatik)', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 3,
                'default' => '',
                'placeholder' => 'Otomatik doldurulacak...',
                'description' => __('Güzergah seçildiğinde otomatik doldurulur.', 'dwd-transfer'),
            ]);

            $this->end_controls_section();

            // CARD STİL - Wrapper
            $this->start_controls_section('card_style_wrapper', [
                'label' => __('🎨 Card - Wrapper', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_responsive_control('card_wrapper_width', [
                'label' => __('Genişlik', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => ['px'=>['min'=>100,'max'=>2000],'%'=>['min'=>10,'max'=>100]],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'card_wrapper_bg',
                    'label' => __('Arka Plan', 'dwd-transfer'),
                    'types' => ['classic', 'gradient'],
                    'selector' => '{{WRAPPER}} .dwd-card-inner::before',
                ]
            );

            $this->add_control('card_wrapper_overlay_color', [
                'label' => __('Overlay Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-inner::after' => 'background-color: {{VALUE}};',
                ],
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'card_wrapper_border',
                    'selector' => '{{WRAPPER}} .dwd-card-inner',
                ]
            );

            $this->add_responsive_control('card_wrapper_border_radius', [
                'label' => __('Köşe Yuvarlaklığı', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->add_responsive_control('card_wrapper_padding', [
                'label' => __('İç Boşluk', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();

            // CARD STİL - Route Text
            $this->start_controls_section('card_style_route', [
                'label' => __('🎨 Card - Güzergah', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'card_route_text_typo',
                    'selector' => '{{WRAPPER}} .dwd-card-route-text',
                ]
            );

            $this->add_control('card_route_text_color', [
                'label' => __('Yazı Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-route-text' => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_responsive_control('card_route_text_spacing', [
                'label' => __('Alt Boşluk', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-route' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();

            // CARD STİL - Route Icon
            $this->start_controls_section('card_style_route_icon', [
                'label' => __('🎨 Card - Güzergah Icon', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_responsive_control('card_route_icon_size', [
                'label' => __('Icon Boyutu', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px'=>['min'=>10,'max'=>100]],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-route-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->add_control('card_route_icon_color', [
                'label' => __('Icon Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-route-icon' => 'color: {{VALUE}};',
                ],
            ]);

            $this->end_controls_section();

            // CARD STİL - Stats
            $this->start_controls_section('card_style_stats', [
                'label' => __('🎨 Card - KM & Süre', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'card_stat_text_typo',
                    'selector' => '{{WRAPPER}} .dwd-card-stat-text',
                ]
            );

            $this->add_control('card_stat_text_color', [
                'label' => __('Yazı Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-stat-text' => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_responsive_control('card_stat_icon_size', [
                'label' => __('Icon Boyutu', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px'=>['min'=>10,'max'=>60]],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-stat-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->add_control('card_stat_icon_color', [
                'label' => __('Icon Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-stat-icon' => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_responsive_control('card_stat_spacing', [
                'label' => __('Alt Boşluk', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-stats' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();

            // CARD STİL - Price
            $this->start_controls_section('card_style_price', [
                'label' => __('🎨 Card - Fiyat', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'card_price_label_typo',
                    'label' => __('Etiket Tipografi', 'dwd-transfer'),
                    'selector' => '{{WRAPPER}} .dwd-card-price-label',
                ]
            );

            $this->add_control('card_price_label_color', [
                'label' => __('Etiket Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-price-label' => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'card_price_value_typo',
                    'label' => __('Fiyat Tipografi', 'dwd-transfer'),
                    'selector' => '{{WRAPPER}} .dwd-card-price-value',
                ]
            );

            $this->add_control('card_price_value_color', [
                'label' => __('Fiyat Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-price-value' => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_responsive_control('price_section_align', [
                'label' => __('Fiyat Alanı Hizalama', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => ['title' => __('Sol', 'dwd-transfer'), 'icon' => 'eicon-text-align-left'],
                    'center' => ['title' => __('Orta', 'dwd-transfer'), 'icon' => 'eicon-text-align-center'],
                    'right' => ['title' => __('Sağ', 'dwd-transfer'), 'icon' => 'eicon-text-align-right'],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-price-section' => 'text-align: {{VALUE}};',
                ],
            ]);

            $this->add_control('price_box_bg', [
                'label' => __('Fiyat Alanı Arka Plan', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-price-box' => 'background-color: {{VALUE}};',
                ],
            ]);

            $this->add_responsive_control('price_box_padding', [
                'label' => __('Fiyat Alanı Padding', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-price-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->add_responsive_control('price_box_radius', [
                'label' => __('Fiyat Alanı Köşe Yuvarlama', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-price-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->add_control('price_box_auto_width', [
                'label' => __('Otomatik Genişlik', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Açık', 'dwd-transfer'),
                'label_off' => __('Kapalı', 'dwd-transfer'),
                'return_value' => 'yes',
                'default' => 'no',
            ]);

            $this->add_responsive_control('card_price_spacing', [
                'label' => __('Alt Boşluk', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-price-section' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();

            // CARD STİL - Button
            $this->start_controls_section('card_style_button', [
                'label' => __('🎨 Card - Buton', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'card_button_typo',
                    'selector' => '{{WRAPPER}} .dwd-card-button',
                ]
            );

            $this->start_controls_tabs('card_button_tabs');

            $this->start_controls_tab('card_button_normal', ['label' => __('Normal', 'dwd-transfer')]);

            $this->add_control('card_button_color', [
                'label' => __('Yazı Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-button' => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'card_button_bg',
                    'selector' => '{{WRAPPER}} .dwd-card-button',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab('card_button_hover', ['label' => __('Hover', 'dwd-transfer')]);

            $this->add_control('card_button_color_hover', [
                'label' => __('Yazı Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-button:hover' => 'color: {{VALUE}};',
                ],
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'card_button_bg_hover',
                    'selector' => '{{WRAPPER}} .dwd-card-button:hover',
                ]
            );

            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'card_button_border',
                    'selector' => '{{WRAPPER}} .dwd-card-button',
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control('card_button_border_radius', [
                'label' => __('Köşe Yuvarlaklığı', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->add_responsive_control('card_button_padding', [
                'label' => __('Padding', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-card-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();

            // RIBBON
            $this->start_controls_section('card_ribbon_section', [
                'label' => __('🏷️ Ribbon', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'card']
            ]);

            $this->add_control('ribbon_text', [
                'label' => __('Ribbon Metni', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]);

            $this->add_control('ribbon_style', [
                'label' => __('Stil', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'diagonal' => 'Çapraz (Diagonal)',
                    'straight' => 'Düz Bant (Full Width)',
                ],
                'default' => 'diagonal',
            ]);

            $this->add_control('ribbon_position', [
                'label' => __('Konum', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top-right' => 'Sağ Üst',
                    'top-left' => 'Sol Üst',
                    'bottom-right' => 'Sağ Alt',
                    'bottom-left' => 'Sol Alt',
                    'top-full' => 'Üst (Düz Bant için)',
                    'bottom-full' => 'Alt (Düz Bant için)',
                ],
                'default' => 'top-right',
            ]);

            $this->add_control('ribbon_color', [
                'label' => __('Yazı Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]);

            $this->add_control('ribbon_bg', [
                'label' => __('Arka Plan', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e91e63',
            ]);

            $this->end_controls_section();

            // ============================================
            // BUTTON AYARLARI
            // ============================================
            $this->start_controls_section('button_content_section', [
                'label' => __('📋 Button İçerik', 'dwd-transfer'),
                'condition' => ['view_type' => 'button']
            ]);

            $this->add_control('button_row1_icon', [
                'label' => __('1. Satır - Icon', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '<i class="fa-solid fa-calendar"></i>',
                'rows' => 2,
            ]);

            $this->add_control('button_row1_text', [
                'label' => __('1. Satır - Text', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'icon + text',
            ]);

            $this->add_control('button_row2_icon', [
                'label' => __('2. Satır - Icon', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '<i class="fa-solid fa-clock"></i>',
                'rows' => 2,
            ]);

            $this->add_control('button_row2_text', [
                'label' => __('2. Satır - Text', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'icon + text',
            ]);

            $this->add_control('button_arrow_icon', [
                'label' => __('Arrow Icon', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '<i class="fa-solid fa-arrow-right"></i>',
                'rows' => 2,
            ]);

            $this->end_controls_section();

            // BUTTON STİL - Wrapper
            $this->start_controls_section('button_style_wrapper', [
                'label' => __('🎨 Button - Wrapper', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'button']
            ]);

            $this->add_responsive_control('button_wrapper_width', [
                'label' => __('Genişlik', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => ['px'=>['min'=>100,'max'=>2000],'%'=>['min'=>10,'max'=>100]],
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'button_wrapper_bg',
                    'selector' => '{{WRAPPER}} .dwd-button-wrapper',
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'button_wrapper_border',
                    'selector' => '{{WRAPPER}} .dwd-button-wrapper',
                ]
            );

            $this->add_responsive_control('button_wrapper_border_radius', [
                'label' => __('Köşe Yuvarlaklığı', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();

            // BUTTON STİL - Content
            $this->start_controls_section('button_style_content', [
                'label' => __('🎨 Button - İçerik', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'button']
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'button_content_bg',
                    'selector' => '{{WRAPPER}} .dwd-button-content',
                ]
            );

            $this->add_responsive_control('button_content_padding', [
                'label' => __('Padding', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->add_responsive_control('button_row_gap', [
                'label' => __('Satır Arası Boşluk', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-content' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->end_controls_section();

            // BUTTON STİL - Row Text
            $this->start_controls_section('button_style_row', [
                'label' => __('🎨 Button - Satır Metni', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'button']
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'button_row_text_typo',
                    'selector' => '{{WRAPPER}} .dwd-button-row-text',
                ]
            );

            $this->add_control('button_row_text_color', [
                'label' => __('Yazı Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-row-text' => 'color: {{VALUE}};',
                ],
            ]);

            $this->end_controls_section();

            // BUTTON STİL - Row Icon
            $this->start_controls_section('button_style_row_icon', [
                'label' => __('🎨 Button - Satır Icon', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'button']
            ]);

            $this->add_responsive_control('button_row_icon_size', [
                'label' => __('Icon Boyutu', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px'=>['min'=>10,'max'=>60]],
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-row-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->add_control('button_row_icon_color', [
                'label' => __('Icon Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-row-icon' => 'color: {{VALUE}};',
                ],
            ]);

            $this->end_controls_section();

            // BUTTON STİL - Arrow
            $this->start_controls_section('button_style_arrow', [
                'label' => __('🎨 Button - Sağ Arrow', 'dwd-transfer'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['view_type' => 'button']
            ]);

            $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'button_arrow_bg',
                    'selector' => '{{WRAPPER}} .dwd-button-arrow',
                ]
            );

            $this->add_responsive_control('button_arrow_width', [
                'label' => __('Genişlik', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px'=>['min'=>30,'max'=>200]],
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-arrow' => 'min-width: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->add_responsive_control('button_arrow_icon_size', [
                'label' => __('Icon Boyutu', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px'=>['min'=>16,'max'=>100]],
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]);

            $this->add_control('button_arrow_icon_color', [
                'label' => __('Icon Rengi', 'dwd-transfer'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dwd-button-arrow' => 'color: {{VALUE}};',
                ],
            ]);

            $this->end_controls_section();

            // ============================================
            // MODAL AYARLARI
            // ============================================
            $this->start_controls_section('modal_section', [
                'label'=>__('⚙️ Modal','dwd-transfer'),
                'tab'=>\Elementor\Controls_Manager::TAB_STYLE
            ]);

            $this->add_control('modal_fullscreen', [
                'label'=>__('Tam Ekran','dwd-transfer'),
                'type'=>\Elementor\Controls_Manager::SWITCHER,
                'return_value'=>'yes',
                'default'=>'yes'
            ]);

            $this->add_control('modal_bg_color', [
                'label'=>__('Overlay Rengi','dwd-transfer'),
                'type'=>\Elementor\Controls_Manager::COLOR,
                'default'=>'rgba(0,0,0,0.6)'
            ]);

            $this->end_controls_section();
        }

        protected function render() {
            $s = $this->get_settings_for_display();
            $pdo = $this->connect_db();

            $start = '';
            $end = '';
            $fiyat = 0;
            $sure = 0;
            $km = 0;
            $route_id = intval($s['route_id'] ?? 0);

            if ($pdo && $route_id > 0) {
                try {
                    $stmt = $pdo->prepare("SELECT baslangic_tr, bitis_tr, sure, km, gunduz_1_3_kisi, gunduz_4_5_kisi, gunduz_6_8_kisi FROM sabit_fiyatlar WHERE id = :id LIMIT 1");
                    $stmt->bindValue(':id', $route_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $r = $stmt->fetch();

                    if ($r) {
                        $start = trim($r['baslangic_tr'] ?? '');
                        $end = trim($r['bitis_tr'] ?? '');
                        $sure = intval($r['sure'] ?? 0);
                        $km = floatval($r['km'] ?? 0);

                        $pass = intval($s['passenger'] ?? 1);
                        if ($pass >= 1 && $pass <= 3) {
                            $fiyat = floatval($r['gunduz_1_3_kisi'] ?? 0);
                        } elseif ($pass >= 4 && $pass <= 5) {
                            $fiyat = floatval($r['gunduz_4_5_kisi'] ?? 0);
                        } elseif ($pass >= 6 && $pass <= 8) {
                            $fiyat = floatval($r['gunduz_6_8_kisi'] ?? 0);
                        } else {
                            $fiyat = floatval($r['gunduz_1_3_kisi'] ?? 0);
                        }

                        if (intval($s['transfertipi'] ?? 0) === 1) {
                            $fiyat = $fiyat * 2;
                        }
                    }
                } catch (Exception $e) {
                    error_log('DWD Transfer render Error: ' . $e->getMessage());
                }
            }

            $vip_path = trim($s['viprez_path'] ?? '/transfer/viprez.php');
            if (preg_match('#^https?://#i', $vip_path)) {
                $base = $vip_path;
            } else {
                $base = site_url($vip_path);
            }

            $raw_link = $base . '?route_id=' . intval($route_id)
                . '&passenger=' . intval($s['passenger'] ?? 1)
                . '&baggage=' . intval($s['baggage'] ?? 1)
                . '&transfertipi=' . intval($s['transfertipi'] ?? 0)
                . '#topless';

            $modal_bg = esc_attr($s['modal_bg_color'] ?? 'rgba(0,0,0,0.6)');
            $modal_full = (!empty($s['modal_fullscreen']) && $s['modal_fullscreen']==='yes') ? 'yes' : 'no';
            $modal_w = '90%';
            $modal_h = '90%';

            $editor_class = \Elementor\Plugin::$instance->editor->is_edit_mode() ? ' elementor-editor-active' : '';

            if ($s['view_type'] === 'card') {
                $sure_formatted = floor($sure / 60) . 'h ' . ($sure % 60) . 'min';
                $km_formatted = number_format($km, 1, ',', '.') . ' km';
                $fiyat_formatted = number_format($fiyat, 0, ',', '.');

                echo '<div class="dwd-card-wrapper'.$editor_class.'" data-modal-link="'.esc_url($raw_link).'" data-modal-bg="'.$modal_bg.'" data-modal-full="'.$modal_full.'" data-modal-width="'.$modal_w.'" data-modal-height="'.$modal_h.'">';
                echo '<div class="dwd-card-inner">';
                
                // Ribbon
                if (!empty($s['ribbon_text'])) {
                    $rtext = esc_html($s['ribbon_text']);
                    $rbg = esc_attr($s['ribbon_bg'] ?? '#e91e63');
                    $rcolor = esc_attr($s['ribbon_color'] ?? '#fff');
                    $rstyle = esc_attr($s['ribbon_style'] ?? 'diagonal');
                    $rpos = esc_attr($s['ribbon_position'] ?? 'top-right');
                    echo '<div class="dwd-card-ribbon '.$rstyle.' '.$rpos.'" style="background:'.$rbg.';color:'.$rcolor.';">'.$rtext.'</div>';
                }
                
                // Route
                echo '<div class="dwd-card-route">';
                echo '<span class="dwd-card-route-text">'.esc_html($s['card_route_text_start'] ?? $start).'</span>';
                echo '<span class="dwd-card-route-icon">'.($s['card_route_icon'] ?? '').'</span>';
                echo '<span class="dwd-card-route-text">'.esc_html($s['card_route_text_end'] ?? $end).'</span>';
                echo '</div>';
                
                // Stats
                echo '<div class="dwd-card-stats">';
                echo '<div class="dwd-card-stat-item">';
                echo '<span class="dwd-card-stat-icon">'.($s['card_km_icon'] ?? '').'</span>';
                echo '<span class="dwd-card-stat-text">'.$km_formatted.'</span>';
                echo '</div>';
                echo '<div class="dwd-card-stat-item">';
                echo '<span class="dwd-card-stat-icon">'.($s['card_time_icon'] ?? '').'</span>';
                echo '<span class="dwd-card-stat-text">'.$sure_formatted.'</span>';
                echo '</div>';
                echo '</div>';
                
                // Price
                echo '<div class="dwd-card-price-section">';
                echo '<div class="dwd-card-price-box">';
                echo '<div class="dwd-card-price-label">'.esc_html($s['card_price_label'] ?? 'AB').'</div>';
                echo '<div class="dwd-card-price-value">';
                echo $fiyat_formatted.'<span class="dwd-card-price-currency">'.esc_html($s['card_currency'] ?? '€').'</span>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                // Button
                echo '<a href="'.esc_url($raw_link).'" class="dwd-card-button">'.esc_html($s['card_button_text'] ?? 'Jetzt buchen').'</a>';
                
                echo '</div>';
                echo '<div class="dwd-link-preview">'.esc_html($raw_link).'</div>';
                echo '</div>';
            } else {
                // BUTTON
                echo '<a href="'.esc_url($raw_link).'" class="dwd-button-wrapper'.$editor_class.'" data-modal-link="'.esc_url($raw_link).'" data-modal-bg="'.$modal_bg.'" data-modal-full="'.$modal_full.'" data-modal-width="'.$modal_w.'" data-modal-height="'.$modal_h.'">';
                echo '<div class="dwd-button-content">';
                echo '<div class="dwd-button-row">';
                echo '<span class="dwd-button-row-icon">'.($s['button_row1_icon'] ?? '').'</span>';
                echo '<span class="dwd-button-row-text">'.esc_html($s['button_row1_text'] ?? '').'</span>';
                echo '</div>';
                echo '<div class="dwd-button-row">';
                echo '<span class="dwd-button-row-icon">'.($s['button_row2_icon'] ?? '').'</span>';
                echo '<span class="dwd-button-row-text">'.esc_html($s['button_row2_text'] ?? '').'</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="dwd-button-arrow">'.($s['button_arrow_icon'] ?? '').'</div>';
                echo '</a>';
                echo '<div class="dwd-link-preview">'.esc_html($raw_link).'</div>';
            }
        }
    }

    $widgets_manager->register(new \DWD_Transfer_Widget_Complete());
});
