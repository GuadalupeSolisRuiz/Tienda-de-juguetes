<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description"
    content="Tienda de Juguetes — Descubre los mejores juguetes para niños y niñas. Peluches, educativos, electrónicos y más con envío a todo el país." />
  <title>Tienda de Juguetes — Inicio</title>
  <?php include "include/conect.php"; ?>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Fredoka+One&display=swap"
    rel="stylesheet" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet" />

  <style>
    /* ══════════════════════════════════════════════
       DESIGN TOKENS (same as registro.php)
    ══════════════════════════════════════════════ */
    :root {
      --purple: #7C3AED;
      --purple-light: #8B5CF6;
      --purple-dark: #6D28D9;
      --yellow: #F59E0B;
      --pink: #EC4899;
      --blue: #3B82F6;
      --green: #10B981;
      --bg: #FDF8F0;
      --text: #1F2937;
      --text-light: #6B7280;
      --border: #E5E7EB;
      --red: #EF4444;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Nunito', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ══════════════════════════════════════════════
       NAVBAR (identical to registro.php)
    ══════════════════════════════════════════════ */
    .navbar {
      background: #fff !important;
      border-bottom: 1px solid var(--border);
      height: 64px;
      font-family: 'Nunito', sans-serif;
    }

    .logo-icon {
      font-size: 2rem;
    }

    .logo-text {
      display: flex;
      flex-direction: column;
      line-height: 1;
    }

    .logo-top {
      font-weight: 700;
      font-size: 0.65rem;
      color: var(--text);
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .logo-bottom {
      font-family: 'Fredoka One', cursive;
      font-size: 1.4rem;
      background: linear-gradient(90deg, #EF4444, #F59E0B, #10B981, #3B82F6, #8B5CF6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .navbar-nav .nav-link {
      font-weight: 600;
      font-size: 0.9rem;
      color: var(--text) !important;
      transition: color 0.2s;
    }

    .navbar-nav .nav-link:hover {
      color: var(--purple) !important;
    }

    .nav-icon-btn {
      background: none;
      border: none;
      padding: 0;
      color: var(--text);
      transition: color 0.2s;
      cursor: pointer;
      line-height: 1;
    }

    .nav-icon-btn:hover {
      color: var(--purple);
    }

    .nav-icon-btn i {
      font-size: 1.3rem;
    }

    .cart-wrapper {
      position: relative;
      display: inline-flex;
    }

    .cart-count {
      position: absolute;
      top: -6px;
      right: -6px;
      background: var(--red);
      color: #fff;
      font-size: 0.58rem;
      font-weight: 800;
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ══════════════════════════════════════════════
       PROMO TICKER
    ══════════════════════════════════════════════ */
    .promo-ticker {
      background: linear-gradient(90deg, var(--purple), var(--purple-light), var(--pink));
      color: #fff;
      padding: 8px 0;
      overflow: hidden;
      position: relative;
    }

    .ticker-track {
      display: flex;
      gap: 60px;
      animation: ticker 25s linear infinite;
      white-space: nowrap;
    }

    .ticker-item {
      font-size: 0.82rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 8px;
      flex-shrink: 0;
    }

    .ticker-item i {
      font-size: 1rem;
    }

    @keyframes ticker {
      0% {
        transform: translateX(0);
      }

      100% {
        transform: translateX(-50%);
      }
    }

    /* ══════════════════════════════════════════════
       HERO SECTION
    ══════════════════════════════════════════════ */
    .hero {
      background: linear-gradient(135deg, #FEF9EC 0%, #FFF3E0 50%, #FEF0F8 100%);
      min-height: 520px;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
    }

    .hero .decorations {
      position: absolute;
      inset: 0;
      pointer-events: none;
    }

    .hero .deco-star {
      position: absolute;
      top: 40px;
      left: 80px;
      font-size: 2.5rem;
      animation: float 3s ease-in-out infinite;
    }

    .hero .deco-star2 {
      position: absolute;
      top: 120px;
      right: 120px;
      font-size: 1.8rem;
      animation: float 3.5s ease-in-out infinite 0.5s;
    }

    .hero .deco-star3 {
      position: absolute;
      bottom: 80px;
      left: 200px;
      font-size: 2rem;
      animation: float 4s ease-in-out infinite 1s;
    }

    .hero .deco-cloud1 {
      position: absolute;
      top: 30px;
      right: 300px;
      width: 110px;
      animation: float 4s ease-in-out infinite 0.5s;
    }

    .hero .deco-cloud2 {
      position: absolute;
      bottom: 60px;
      right: 80px;
      width: 90px;
      animation: float 3.5s ease-in-out infinite 1s;
    }

    .hero .deco-dots {
      position: absolute;
      bottom: 40px;
      left: 60px;
      display: grid;
      grid-template-columns: repeat(4, 8px);
      gap: 10px;
    }

    .hero .deco-dots span {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: rgba(124, 58, 237, 0.15);
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    @keyframes bounceIn {
      0% {
        opacity: 0;
        transform: scale(0.3);
      }

      50% {
        opacity: 1;
        transform: scale(1.05);
      }

      70% {
        transform: scale(0.95);
      }

      100% {
        transform: scale(1);
      }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .hero-content {
      position: relative;
      z-index: 2;
      padding: 60px 0;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: 50px;
      padding: 6px 16px;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--purple);
      margin-bottom: 20px;
      animation: slideUp 0.6s ease-out;
    }

    .hero-badge .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--green);
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.4;
      }
    }

    .hero h1 {
      font-family: 'Fredoka One', cursive;
      font-size: 3.2rem;
      color: var(--text);
      line-height: 1.15;
      margin-bottom: 18px;
      animation: slideUp 0.6s ease-out 0.1s both;
    }

    .hero h1 .highlight {
      position: relative;
      display: inline-block;
    }

    .hero h1 .highlight::after {
      content: '';
      position: absolute;
      bottom: 2px;
      left: 0;
      width: 100%;
      height: 12px;
      background: rgba(245, 158, 11, 0.3);
      border-radius: 4px;
      z-index: -1;
    }

    .hero-description {
      font-size: 1.05rem;
      color: var(--text-light);
      line-height: 1.7;
      max-width: 480px;
      margin-bottom: 28px;
      animation: slideUp 0.6s ease-out 0.2s both;
    }

    .hero-actions {
      display: flex;
      align-items: center;
      gap: 14px;
      animation: slideUp 0.6s ease-out 0.3s both;
    }

    .btn-primary-custom {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 32px;
      background: var(--purple);
      color: #fff;
      border: none;
      border-radius: 12px;
      font-family: 'Nunito', sans-serif;
      font-size: 0.95rem;
      font-weight: 800;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
      box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
    }

    .btn-primary-custom:hover {
      background: var(--purple-dark);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(124, 58, 237, 0.45);
      color: #fff;
    }

    .btn-primary-custom:active {
      transform: translateY(0);
    }

    .btn-secondary-custom {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 28px;
      background: #fff;
      color: var(--text);
      border: 1.5px solid var(--border);
      border-radius: 12px;
      font-family: 'Nunito', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: border-color 0.2s, transform 0.1s, box-shadow 0.2s;
    }

    .btn-secondary-custom:hover {
      border-color: var(--purple);
      color: var(--purple);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    .hero-toys {
      position: relative;
      z-index: 2;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      animation: bounceIn 0.8s ease-out 0.4s both;
    }

    .hero-toys .toy-main {
      font-size: 14rem;
      filter: drop-shadow(0 12px 30px rgba(0, 0, 0, 0.12));
      animation: float 4s ease-in-out infinite;
    }

    .hero-toys .toy-side1 {
      font-size: 5rem;
      margin-right: -30px;
      margin-bottom: 20px;
      filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.12));
      animation: float 3.5s ease-in-out infinite 0.5s;
    }

    .hero-toys .toy-side2 {
      font-size: 4rem;
      margin-left: -20px;
      margin-bottom: 40px;
      filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.12));
      animation: float 3.2s ease-in-out infinite 1s;
    }

    .hero-stats {
      display: flex;
      gap: 40px;
      margin-top: 32px;
      animation: slideUp 0.6s ease-out 0.5s both;
    }

    .hero-stat {
      text-align: center;
    }

    .hero-stat .number {
      font-family: 'Fredoka One', cursive;
      font-size: 1.6rem;
      color: var(--purple);
    }

    .hero-stat .label {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--text-light);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    /* ══════════════════════════════════════════════
       SECTION HEADERS
    ══════════════════════════════════════════════ */
    .section-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .section-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 5px 14px;
      border-radius: 50px;
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      margin-bottom: 12px;
    }

    .section-badge.purple {
      background: #EDE9FE;
      color: #7C3AED;
    }

    .section-badge.pink {
      background: #FCE7F3;
      color: #EC4899;
    }

    .section-badge.yellow {
      background: #FEF3C7;
      color: #D97706;
    }

    .section-badge.green {
      background: #D1FAE5;
      color: #059669;
    }

    .section-header h2 {
      font-family: 'Fredoka One', cursive;
      font-size: 2.2rem;
      color: var(--text);
      margin-bottom: 8px;
    }

    .section-header p {
      font-size: 0.95rem;
      color: var(--text-light);
      max-width: 500px;
      margin: 0 auto;
    }

    /* ══════════════════════════════════════════════
       CATEGORIES
    ══════════════════════════════════════════════ */
    .categories-section {
      padding: 70px 0;
    }

    .category-card {
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: 20px;
      padding: 30px 20px;
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
      cursor: pointer;
      text-decoration: none;
      display: block;
      height: 100%;
    }

    .category-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
      border-color: var(--purple-light);
    }

    .category-icon {
      width: 72px;
      height: 72px;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      font-size: 2.2rem;
      transition: transform 0.3s;
    }

    .category-card:hover .category-icon {
      transform: scale(1.1) rotate(-5deg);
    }

    .category-icon.bg-purple {
      background: #EDE9FE;
    }

    .category-icon.bg-pink {
      background: #FCE7F3;
    }

    .category-icon.bg-yellow {
      background: #FEF3C7;
    }

    .category-icon.bg-blue {
      background: #DBEAFE;
    }

    .category-icon.bg-green {
      background: #D1FAE5;
    }

    .category-icon.bg-red {
      background: #FEE2E2;
    }

    .category-card h3 {
      font-family: 'Nunito', sans-serif;
      font-size: 1rem;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 4px;
    }

    .category-card p {
      font-size: 0.78rem;
      color: var(--text-light);
      margin: 0;
    }

    /* ══════════════════════════════════════════════
       PRODUCTS
    ══════════════════════════════════════════════ */
    .products-section {
      padding: 70px 0;
      background: #fff;
    }

    .product-card {
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: 20px;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .product-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .product-image {
      background: linear-gradient(135deg, #FEF9EC 0%, #FFF3E0 50%, #FEF0F8 100%);
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    .product-image .emoji {
      font-size: 5.5rem;
      transition: transform 0.4s;
      filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.1));
    }

    .product-card:hover .product-image .emoji {
      transform: scale(1.12) rotate(-5deg);
    }

    .product-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
    }

    .product-badge.new {
      background: var(--green);
      color: #fff;
    }

    .product-badge.sale {
      background: var(--red);
      color: #fff;
    }

    .product-badge.popular {
      background: var(--yellow);
      color: #fff;
    }

    .product-wishlist {
      position: absolute;
      top: 12px;
      right: 12px;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: #fff;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s, color 0.2s;
      color: var(--text-light);
    }

    .product-wishlist:hover {
      transform: scale(1.15);
      color: var(--red);
    }

    .product-info {
      padding: 18px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .product-category-tag {
      font-size: 0.7rem;
      font-weight: 800;
      color: var(--purple);
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 6px;
    }

    .product-info h3 {
      font-family: 'Nunito', sans-serif;
      font-size: 1rem;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 6px;
    }

    .product-info .description {
      font-size: 0.8rem;
      color: var(--text-light);
      line-height: 1.5;
      margin-bottom: 12px;
      flex-grow: 1;
    }

    .product-rating {
      display: flex;
      align-items: center;
      gap: 4px;
      margin-bottom: 12px;
    }

    .product-rating i {
      font-size: 0.82rem;
      color: var(--yellow);
    }

    .product-rating span {
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--text-light);
      margin-left: 4px;
    }

    .product-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 12px;
      border-top: 1px solid var(--border);
    }

    .product-price {
      font-family: 'Fredoka One', cursive;
      font-size: 1.25rem;
      color: var(--text);
    }

    .product-price .old {
      font-family: 'Nunito', sans-serif;
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-light);
      text-decoration: line-through;
      margin-left: 6px;
    }

    .btn-add-cart {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      background: var(--purple);
      color: #fff;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      box-shadow: 0 3px 10px rgba(124, 58, 237, 0.3);
    }

    .btn-add-cart:hover {
      background: var(--purple-dark);
      transform: scale(1.1);
    }

    .btn-add-cart i {
      font-size: 1.1rem;
    }

    /* ══════════════════════════════════════════════
       PROMO BANNER
    ══════════════════════════════════════════════ */
    .promo-banner {
      background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 40%, #EC4899 100%);
      padding: 60px 0;
      position: relative;
      overflow: hidden;
    }

    .promo-banner::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -10%;
      width: 400px;
      height: 400px;
      background: rgba(255, 255, 255, 0.06);
      border-radius: 50%;
    }

    .promo-banner::after {
      content: '';
      position: absolute;
      bottom: -30%;
      left: -5%;
      width: 300px;
      height: 300px;
      background: rgba(255, 255, 255, 0.04);
      border-radius: 50%;
    }

    .promo-content {
      position: relative;
      z-index: 2;
    }

    .promo-banner h2 {
      font-family: 'Fredoka One', cursive;
      font-size: 2.6rem;
      color: #fff;
      margin-bottom: 12px;
    }

    .promo-banner p {
      font-size: 1.05rem;
      color: rgba(255, 255, 255, 0.85);
      max-width: 460px;
      margin-bottom: 24px;
    }

    .btn-promo {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 14px 32px;
      background: #fff;
      color: var(--purple);
      border: none;
      border-radius: 12px;
      font-family: 'Nunito', sans-serif;
      font-size: 0.95rem;
      font-weight: 800;
      cursor: pointer;
      text-decoration: none;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    }

    .btn-promo:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
      color: var(--purple-dark);
    }

    .promo-toys {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .promo-toys span {
      font-size: 6rem;
      filter: drop-shadow(0 8px 20px rgba(0, 0, 0, 0.2));
      animation: float 3.5s ease-in-out infinite;
    }

    .promo-toys span:nth-child(2) {
      animation-delay: 0.5s;
      font-size: 7rem;
    }

    .promo-toys span:nth-child(3) {
      animation-delay: 1s;
    }

    /* ══════════════════════════════════════════════
       WHY US SECTION
    ══════════════════════════════════════════════ */
    .why-section {
      padding: 70px 0;
    }

    .why-card {
      background: #fff;
      border: 1.5px solid var(--border);
      border-radius: 20px;
      padding: 32px 24px;
      text-align: center;
      height: 100%;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .why-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .why-icon {
      width: 64px;
      height: 64px;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 18px;
    }

    .why-icon i {
      font-size: 1.5rem;
    }

    .why-icon.purple {
      background: #EDE9FE;
      color: #7C3AED;
    }

    .why-icon.pink {
      background: #FCE7F3;
      color: #EC4899;
    }

    .why-icon.green {
      background: #D1FAE5;
      color: #059669;
    }

    .why-icon.blue {
      background: #DBEAFE;
      color: #2563EB;
    }

    .why-card h3 {
      font-family: 'Nunito', sans-serif;
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 8px;
    }

    .why-card p {
      font-size: 0.82rem;
      color: var(--text-light);
      line-height: 1.6;
      margin: 0;
    }

    /* ══════════════════════════════════════════════
       NEWSLETTER
    ══════════════════════════════════════════════ */
    .newsletter-section {
      padding: 60px 0;
      background: #fff;
    }

    .newsletter-box {
      background: linear-gradient(135deg, #FEF9EC 0%, #FFF3E0 50%, #FEF0F8 100%);
      border-radius: 24px;
      padding: 50px 40px;
      position: relative;
      overflow: hidden;
    }

    .newsletter-box::before {
      content: '✉️';
      position: absolute;
      top: -10px;
      right: 40px;
      font-size: 6rem;
      opacity: 0.12;
      transform: rotate(15deg);
    }

    .newsletter-content {
      position: relative;
      z-index: 2;
    }

    .newsletter-box h2 {
      font-family: 'Fredoka One', cursive;
      font-size: 1.8rem;
      color: var(--text);
      margin-bottom: 8px;
    }

    .newsletter-box p {
      font-size: 0.9rem;
      color: var(--text-light);
      margin-bottom: 24px;
    }

    .newsletter-form {
      display: flex;
      gap: 12px;
      max-width: 480px;
    }

    .newsletter-form input {
      flex: 1;
      padding: 14px 18px;
      border: 1.5px solid var(--border);
      border-radius: 12px;
      font-family: 'Nunito', sans-serif;
      font-size: 0.9rem;
      background: #fff;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .newsletter-form input:focus {
      outline: none;
      border-color: var(--purple);
      box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    .newsletter-form input::placeholder {
      color: #9CA3AF;
    }

    .newsletter-form button {
      padding: 14px 28px;
      background: var(--purple);
      color: #fff;
      border: none;
      border-radius: 12px;
      font-family: 'Nunito', sans-serif;
      font-size: 0.9rem;
      font-weight: 800;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
      box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
      white-space: nowrap;
    }

    .newsletter-form button:hover {
      background: var(--purple-dark);
      transform: translateY(-1px);
    }

    /* ══════════════════════════════════════════════
       FOOTER BAR (identical to registro.php)
    ══════════════════════════════════════════════ */
    .footer-bar {
      background: #F3F4F6;
      border-top: 1px solid var(--border);
      padding: 24px 60px;
    }

    .footer-item {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .footer-icon {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .footer-icon i {
      font-size: 1.25rem;
    }

    .footer-icon.purple {
      background: #EDE9FE;
      color: #7C3AED;
    }

    .footer-icon.pink {
      background: #FCE7F3;
      color: #EC4899;
    }

    .footer-icon.green {
      background: #D1FAE5;
      color: #059669;
    }

    .footer-icon.blue {
      background: #DBEAFE;
      color: #2563EB;
    }

    .footer-text strong {
      display: block;
      font-size: 0.9rem;
      font-weight: 800;
      color: var(--text);
    }

    .footer-text span {
      font-size: 0.78rem;
      color: var(--text-light);
    }

    /* ══════════════════════════════════════════════
       MAIN FOOTER
    ══════════════════════════════════════════════ */
    .main-footer {
      background: var(--text);
      color: #D1D5DB;
      padding: 50px 0 24px;
    }

    .main-footer .footer-brand .logo-top {
      color: #D1D5DB;
    }

    .main-footer h5 {
      font-family: 'Nunito', sans-serif;
      font-size: 0.85rem;
      font-weight: 800;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      margin-bottom: 16px;
    }

    .main-footer ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .main-footer ul li {
      margin-bottom: 8px;
    }

    .main-footer ul li a {
      color: #9CA3AF;
      text-decoration: none;
      font-size: 0.85rem;
      font-weight: 600;
      transition: color 0.2s;
    }

    .main-footer ul li a:hover {
      color: #fff;
    }

    .footer-social {
      display: flex;
      gap: 10px;
      margin-top: 16px;
    }

    .footer-social a {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #9CA3AF;
      text-decoration: none;
      transition: background 0.2s, color 0.2s, transform 0.2s;
    }

    .footer-social a:hover {
      background: var(--purple);
      color: #fff;
      transform: translateY(-2px);
    }

    .footer-social a i {
      font-size: 1rem;
    }

    .footer-bottom {
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      margin-top: 32px;
      padding-top: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .footer-bottom p {
      font-size: 0.78rem;
      color: #6B7280;
      margin: 0;
    }

    .footer-payment {
      display: flex;
      gap: 8px;
    }

    .footer-payment span {
      background: rgba(255, 255, 255, 0.08);
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.72rem;
      font-weight: 700;
      color: #9CA3AF;
    }

    /* ══════════════════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════════════════ */
    @media (max-width: 991.98px) {
      .hero h1 {
        font-size: 2.4rem;
      }

      .hero-toys .toy-main {
        font-size: 9rem;
      }

      .hero-toys .toy-side1 {
        font-size: 3.5rem;
      }

      .hero-toys .toy-side2 {
        font-size: 3rem;
      }

      .hero-stats {
        gap: 24px;
      }

      .promo-banner h2 {
        font-size: 2rem;
      }

      .footer-bar {
        padding: 20px 16px;
      }
    }

    @media (max-width: 767.98px) {
      .hero {
        min-height: auto;
      }

      .hero h1 {
        font-size: 2rem;
      }

      .hero-toys {
        margin-top: 30px;
      }

      .hero-toys .toy-main {
        font-size: 7rem;
      }

      .hero-actions {
        flex-direction: column;
        align-items: flex-start;
      }

      .hero-stats {
        flex-direction: column;
        gap: 12px;
        text-align: left;
      }

      .hero-stat {
        text-align: left;
      }

      .newsletter-form {
        flex-direction: column;
      }

      .promo-toys span {
        font-size: 4rem !important;
      }

      .footer-bottom {
        flex-direction: column;
        gap: 12px;
        text-align: center;
      }
    }

    @media (max-width: 575.98px) {
      .footer-item .footer-text strong {
        font-size: 0.78rem;
      }

      .hero h1 {
        font-size: 1.8rem;
      }
    }
  </style>
</head>

<body>

  <!-- ── NAVBAR ── -->
  <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container-fluid px-4">

      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.php" id="logo">
        <span class="logo-icon">🐻</span>
        <span class="logo-text">
          <span class="logo-top">Tienda de</span>
          <span class="logo-bottom">JUGUETES</span>
        </span>
      </a>

      <!-- Toggler (mobile) -->
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Nav links -->
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mx-auto gap-lg-3">
          <li class="nav-item"><a class="nav-link" href="index.php" id="nav-inicio">Inicio</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="nav-juguetes">Juguetes</a>
            <ul class="dropdown-menu shadow-sm border-0">
              <li><a class="dropdown-item" href="#" id="nav-ninas">Niñas</a></li>
              <li><a class="dropdown-item" href="#" id="nav-ninos">Niños</a></li>
              <li><a class="dropdown-item" href="#" id="nav-bebes">Bebés</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="nav-categorias">Categorías</a>
            <ul class="dropdown-menu shadow-sm border-0">
              <li><a class="dropdown-item" href="#" id="nav-educativos">Educativos</a></li>
              <li><a class="dropdown-item" href="#" id="nav-electronicos">Electrónicos</a></li>
              <li><a class="dropdown-item" href="#" id="nav-peluches">Peluches</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="#" id="nav-ofertas">Ofertas</a></li>
          <li class="nav-item"><a class="nav-link" href="#" id="nav-nosotros">Nosotros</a></li>
          <li class="nav-item"><a class="nav-link" href="#" id="nav-contacto">Contacto</a></li>
        </ul>

        <!-- Icons -->
        <div class="d-flex align-items-center gap-3 ms-lg-3 mt-2 mt-lg-0">
          <button class="nav-icon-btn" id="btn-search"><i class="bi bi-search"></i></button>
          <button class="nav-icon-btn" id="btn-user"><i class="bi bi-person"></i></button>
          <button class="nav-icon-btn cart-wrapper" id="btn-cart">
            <i class="bi bi-cart2"></i>
            <span class="cart-count">0</span>
          </button>
        </div>
      </div>

    </div>
  </nav>


  <!-- ── PROMO TICKER ── -->
  <div class="promo-ticker">
    <div class="ticker-track">
      <span class="ticker-item"><i class="bi bi-gift-fill"></i> ¡Envío GRATIS en compras mayores a $599!</span>
      <span class="ticker-item"><i class="bi bi-stars"></i> Nuevas colecciones de verano disponibles</span>
      <span class="ticker-item"><i class="bi bi-percent"></i> Hasta 40% de descuento en juguetes seleccionados</span>
      <span class="ticker-item"><i class="bi bi-heart-fill"></i> +2,000 clientes felices nos respaldan</span>
      <span class="ticker-item"><i class="bi bi-shield-check"></i> Compra 100% segura y garantizada</span>
      <!-- duplicate for seamless loop -->
      <span class="ticker-item"><i class="bi bi-gift-fill"></i> ¡Envío GRATIS en compras mayores a $599!</span>
      <span class="ticker-item"><i class="bi bi-stars"></i> Nuevas colecciones de verano disponibles</span>
      <span class="ticker-item"><i class="bi bi-percent"></i> Hasta 40% de descuento en juguetes seleccionados</span>
      <span class="ticker-item"><i class="bi bi-heart-fill"></i> +2,000 clientes felices nos respaldan</span>
      <span class="ticker-item"><i class="bi bi-shield-check"></i> Compra 100% segura y garantizada</span>
    </div>
  </div>


  <!-- ── HERO ── -->
  <section class="hero">
    <div class="decorations">
      <div class="deco-star">⭐</div>
      <div class="deco-star2">🌟</div>
      <div class="deco-star3">⭐</div>
      <svg class="deco-cloud1" viewBox="0 0 120 70" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="20" y="25" width="80" height="35" rx="17.5" fill="#BAE6FD" />
        <circle cx="45" cy="30" r="20" fill="#BAE6FD" />
        <circle cx="75" cy="25" r="22" fill="#BAE6FD" />
        <circle cx="95" cy="35" r="15" fill="#BAE6FD" />
      </svg>
      <svg class="deco-cloud2" viewBox="0 0 100 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="15" y="22" width="70" height="30" rx="15" fill="#BAE6FD" />
        <circle cx="35" cy="27" r="18" fill="#BAE6FD" />
        <circle cx="62" cy="22" r="19" fill="#BAE6FD" />
        <circle cx="80" cy="30" r="12" fill="#BAE6FD" />
      </svg>
      <div class="deco-dots">
        <span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span>
      </div>
    </div>

    <div class="container">
      <div class="row align-items-center hero-content">
        <div class="col-lg-6">
          <div class="hero-badge">
            <span class="dot"></span>
            Temporada de verano 2026
          </div>

          <h1>Descubre un mundo<br>de <span class="highlight">diversión</span><br>para tus pequeños</h1>

          <p class="hero-description">
            Encuentra los juguetes más divertidos, educativos y seguros para todas las edades. Calidad garantizada y
            precios increíbles.
          </p>

          <div class="hero-actions">
            <a href="#productos" class="btn-primary-custom" id="btn-explore">
              <i class="bi bi-bag-fill"></i>
              Explorar juguetes
            </a>
            <a href="#categorias" class="btn-secondary-custom" id="btn-categories">
              <i class="bi bi-grid-fill"></i>
              Ver categorías
            </a>
          </div>

          <div class="hero-stats">
            <div class="hero-stat">
              <div class="number">2,500+</div>
              <div class="label">Productos</div>
            </div>
            <div class="hero-stat">
              <div class="number">150+</div>
              <div class="label">Marcas</div>
            </div>
            <div class="hero-stat">
              <div class="number">4.9 ⭐</div>
              <div class="label">Satisfacción</div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="hero-toys">
            <span class="toy-side1">🎮</span>
            <span class="toy-main">🧸</span>
            <span class="toy-side2">🎨</span>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── CATEGORIES ── -->
  <section class="categories-section" id="categorias">
    <div class="container">
      <div class="section-header">
        <span class="section-badge purple"><i class="bi bi-grid-fill"></i> Categorías</span>
        <h2>Explora por categoría</h2>
        <p>Encuentra el juguete perfecto navegando por nuestras categorías más populares.</p>
      </div>

      <div class="row g-4">
        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="category-card" id="cat-peluches">
            <div class="category-icon bg-pink">🧸</div>
            <h3>Peluches</h3>
            <p>+320 productos</p>
          </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="category-card" id="cat-educativos">
            <div class="category-icon bg-blue">🧩</div>
            <h3>Educativos</h3>
            <p>+280 productos</p>
          </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="category-card" id="cat-vehiculos">
            <div class="category-icon bg-red">🚗</div>
            <h3>Vehículos</h3>
            <p>+190 productos</p>
          </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="category-card" id="cat-electronicos">
            <div class="category-icon bg-purple">🎮</div>
            <h3>Electrónicos</h3>
            <p>+150 productos</p>
          </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="category-card" id="cat-munecas">
            <div class="category-icon bg-yellow">👸</div>
            <h3>Muñecas</h3>
            <p>+260 productos</p>
          </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
          <a href="#" class="category-card" id="cat-exterior">
            <div class="category-icon bg-green">⚽</div>
            <h3>Exterior</h3>
            <p>+140 productos</p>
          </a>
        </div>
      </div>
    </div>
  </section>


  <!-- ── FEATURED PRODUCTS ── -->
  <section class="products-section" id="productos">
    <div class="container">
      <div class="section-header">
        <span class="section-badge yellow"><i class="bi bi-star-fill"></i> Destacados</span>
        <h2>Juguetes más populares</h2>
        <p>Los favoritos de nuestros clientes. ¡No te quedes sin el tuyo!</p>
      </div>

      <div class="row g-4">

        <!-- Product 1 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-1">
            <div class="product-image">
              <span class="product-badge new">Nuevo</span>
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">🧸</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Peluches</span>
              <h3>Osito Cariñoso XL</h3>
              <p class="description">Peluche ultra suave de 60cm. Perfecto para abrazar.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <span>(128)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$459</span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product 2 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-2">
            <div class="product-image">
              <span class="product-badge sale">-30%</span>
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">🚗</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Vehículos</span>
              <h3>Auto de Carreras RC</h3>
              <p class="description">Control remoto con luces LED y sonidos reales.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-half"></i>
                <span>(95)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$699 <span class="old">$999</span></span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product 3 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-3">
            <div class="product-image">
              <span class="product-badge popular">Popular</span>
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">🧩</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Educativos</span>
              <h3>Rompecabezas 3D</h3>
              <p class="description">500 piezas para armar un castillo medieval.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <span>(203)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$349</span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product 4 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-4">
            <div class="product-image">
              <span class="product-badge new">Nuevo</span>
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">🎮</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Electrónicos</span>
              <h3>Consola Retro Mini</h3>
              <p class="description">200 juegos clásicos incluidos. Conecta a tu TV.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star"></i>
                <span>(67)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$899</span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product 5 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-5">
            <div class="product-image">
              <span class="product-badge sale">-20%</span>
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">👸</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Muñecas</span>
              <h3>Princesa Encantada</h3>
              <p class="description">Muñeca articulada con vestido brillante y accesorios.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <span>(156)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$399 <span class="old">$499</span></span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product 6 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-6">
            <div class="product-image">
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">🎨</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Creativos</span>
              <h3>Kit de Arte Completo</h3>
              <p class="description">150 piezas: colores, pinceles, acuarelas y más.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-half"></i>
                <span>(89)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$279</span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product 7 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-7">
            <div class="product-image">
              <span class="product-badge popular">Popular</span>
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">⚽</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Exterior</span>
              <h3>Balón Profesional</h3>
              <p class="description">Tamaño oficial, resistente a todo terreno.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <span>(312)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$249</span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

        <!-- Product 8 -->
        <div class="col-6 col-lg-3">
          <div class="product-card" id="product-8">
            <div class="product-image">
              <span class="product-badge sale">-15%</span>
              <button class="product-wishlist" aria-label="Agregar a favoritos"><i class="bi bi-heart"></i></button>
              <span class="emoji">🚀</span>
            </div>
            <div class="product-info">
              <span class="product-category-tag">Educativos</span>
              <h3>Cohete Espacial STEM</h3>
              <p class="description">Kit de construcción con lanzamiento real de agua.</p>
              <div class="product-rating">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star"></i>
                <span>(74)</span>
              </div>
              <div class="product-footer">
                <span class="product-price">$549 <span class="old">$649</span></span>
                <button class="btn-add-cart" aria-label="Agregar al carrito"><i class="bi bi-plus"></i></button>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- View all button -->
      <div class="text-center mt-5">
        <a href="#" class="btn-secondary-custom" id="btn-view-all">
          Ver todos los productos
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>


  <!-- ── PROMO BANNER ── -->
  <section class="promo-banner">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-7 promo-content">
          <h2>🎉 ¡Ofertas de temporada!</h2>
          <p>Aprovecha descuentos de hasta el 40% en juguetes seleccionados. Promoción válida hasta agotar existencias.
          </p>
          <a href="#" class="btn-promo" id="btn-promo-offers">
            <i class="bi bi-bag-check-fill"></i>
            Ver ofertas
          </a>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="promo-toys">
            <span>🎁</span>
            <span>🎉</span>
            <span>🎈</span>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── WHY US ── -->
  <section class="why-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge green"><i class="bi bi-check-circle-fill"></i> ¿Por qué elegirnos?</span>
        <h2>Tu compra en las mejores manos</h2>
        <p>Nos preocupamos por ofrecerte la mejor experiencia de compra.</p>
      </div>

      <div class="row g-4">
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon purple"><i class="bi bi-truck"></i></div>
            <h3>Envío rápido</h3>
            <p>Envíos a todo el país en 2-5 días hábiles. Gratis en compras mayores a $599.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon pink"><i class="bi bi-shield-check"></i></div>
            <h3>Compra segura</h3>
            <p>Tus datos siempre protegidos con encriptación de última generación.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon green"><i class="bi bi-arrow-repeat"></i></div>
            <h3>Devoluciones fáciles</h3>
            <p>30 días para devolver tu producto si no estás satisfecho. Sin preguntas.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="why-card">
            <div class="why-icon blue"><i class="bi bi-headset"></i></div>
            <h3>Soporte 24/7</h3>
            <p>Nuestro equipo está disponible para ayudarte en todo momento.</p>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ── NEWSLETTER ── -->
  <section class="newsletter-section">
    <div class="container">
      <div class="newsletter-box">
        <div class="newsletter-content">
          <h2>📬 ¡No te pierdas nada!</h2>
          <p>Suscríbete a nuestro boletín y recibe ofertas exclusivas, novedades y un 10% de descuento en tu primera
            compra.</p>
          <form class="newsletter-form" id="newsletterForm">
            <input type="email" placeholder="Tu correo electrónico" id="newsletter-email" required />
            <button type="submit" id="btn-subscribe">Suscribirme</button>
          </form>
        </div>
      </div>
    </div>
  </section>


  <!-- ── FOOTER BAR (identical to registro.php) ── -->
  <div class="footer-bar">
    <div class="row row-cols-2 row-cols-lg-4 g-3">

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon purple">
            <i class="bi bi-truck"></i>
          </div>
          <div class="footer-text">
            <strong>Envíos a todo el país</strong>
            <span>Rápidos y seguros</span>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon pink">
            <i class="bi bi-tags-fill"></i>
          </div>
          <div class="footer-text">
            <strong>Las mejores marcas</strong>
            <span>Calidad garantizada</span>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon green">
            <i class="bi bi-shield-check"></i>
          </div>
          <div class="footer-text">
            <strong>Compra segura</strong>
            <span>Tus datos protegidos</span>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="footer-item">
          <div class="footer-icon blue">
            <i class="bi bi-headset"></i>
          </div>
          <div class="footer-text">
            <strong>Atención al cliente</strong>
            <span>Estamos para ayudarte</span>
          </div>
        </div>
      </div>

    </div>
  </div>


  <!-- ── MAIN FOOTER ── -->
  <footer class="main-footer">
    <div class="container">
      <div class="row g-4">

        <!-- Brand -->
        <div class="col-lg-3 col-md-6">
          <a class="d-flex align-items-center gap-2 text-decoration-none mb-3 footer-brand" href="index.php">
            <span class="logo-icon">🐻</span>
            <span class="logo-text">
              <span class="logo-top">Tienda de</span>
              <span class="logo-bottom">JUGUETES</span>
            </span>
          </a>
          <p style="font-size: 0.82rem; color: #9CA3AF; line-height: 1.6;">
            Tu destino favorito para encontrar los mejores juguetes. Diversión garantizada para toda la familia.
          </p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
          </div>
        </div>

        <!-- Links -->
        <div class="col-lg-2 col-md-6 col-6">
          <h5>Tienda</h5>
          <ul>
            <li><a href="#">Novedades</a></li>
            <li><a href="#">Más vendidos</a></li>
            <li><a href="#">Ofertas</a></li>
            <li><a href="#">Categorías</a></li>
            <li><a href="#">Marcas</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6 col-6">
          <h5>Ayuda</h5>
          <ul>
            <li><a href="#">Preguntas frecuentes</a></li>
            <li><a href="#">Envíos</a></li>
            <li><a href="#">Devoluciones</a></li>
            <li><a href="#">Rastrear pedido</a></li>
            <li><a href="#">Contacto</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-6 col-6">
          <h5>Empresa</h5>
          <ul>
            <li><a href="#">Sobre nosotros</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Trabaja con nosotros</a></li>
            <li><a href="#">Prensa</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 col-6">
          <h5>Contacto</h5>
          <ul>
            <li><a href="mailto:hola@tiendadejuguetes.com"><i
                  class="bi bi-envelope me-2"></i>hola@tiendadejuguetes.com</a></li>
            <li><a href="tel:+521234567890"><i class="bi bi-telephone me-2"></i>(123) 456-7890</a></li>
            <li><a href="#"><i class="bi bi-geo-alt me-2"></i>Ciudad de México, MX</a></li>
          </ul>
        </div>

      </div>

      <div class="footer-bottom">
        <p>© 2026 Tienda de Juguetes. Todos los derechos reservados.</p>
        <div class="footer-payment">
          <span>Visa</span>
          <span>Mastercard</span>
          <span>PayPal</span>
          <span>OXXO</span>
        </div>
      </div>
    </div>
  </footer>


  <!-- Bootstrap 5 JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

  <script>
    // ── Smooth scroll for anchor links ──
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    // ── Wishlist toggle ──
    document.querySelectorAll('.product-wishlist').forEach(btn => {
      btn.addEventListener('click', function () {
        const icon = this.querySelector('i');
        if (icon.classList.contains('bi-heart')) {
          icon.classList.replace('bi-heart', 'bi-heart-fill');
          this.style.color = '#EF4444';
        } else {
          icon.classList.replace('bi-heart-fill', 'bi-heart');
          this.style.color = '';
        }
      });
    });

    // ── Add to cart animation ──
    const cartCount = document.querySelector('.cart-count');
    let count = 0;
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
      btn.addEventListener('click', function () {
        count++;
        cartCount.textContent = count;
        // Quick pulse animation
        this.style.transform = 'scale(1.3)';
        setTimeout(() => { this.style.transform = ''; }, 200);
      });
    });

    // ── Newsletter form ──
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
      newsletterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const emailInput = document.getElementById('newsletter-email');
        if (emailInput.value) {
          const btn = this.querySelector('button');
          btn.textContent = '¡Suscrito! ✓';
          btn.style.background = '#10B981';
          emailInput.value = '';
          setTimeout(() => {
            btn.textContent = 'Suscribirme';
            btn.style.background = '';
          }, 3000);
        }
      });
    }

    // ── Scroll reveal animation ──
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -40px 0px' };
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    document.querySelectorAll('.category-card, .product-card, .why-card').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
      observer.observe(el);
    });
  </script>
</body>

</html>