<footer class="footer">
  <div class="container">
    <div class="footer__top">
      <div class="footer__logo">
        <img src="{{ Vite::asset('resources/images/logo-1.png') }}" alt="Logo">
      </div>

      <nav class="footer__nav">
        <ul>
          <li><a href="#services">Services</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="#reviews">Reviews</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </nav>

      <div class="footer__socials">
        <a href="https://facebook.com/tu-pagina" aria-label="Facebook" class="icon">
          {{-- Facebook --}}
          <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
            <path d="M13.5 21v-7h2.6l.4-3h-3v-1.9c0-.9.3-1.5 1.6-1.5H16V4.1C15.7 4 14.8 4 13.9 4 11.6 4 10 5.4 10 8.1V11H7.5v3H10v7h3.5Z"/>
          </svg>
        </a>

        <a href="https://instagram.com/tu-pagina" aria-label="Instagram" class="icon">
          {{-- Instagram --}}
          <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="12" cy="12" r="3.8" fill="none" stroke="currentColor" stroke-width="1.6"/>
            <circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/>
          </svg>
        </a>

        <a href="https://www.linkedin.com/company/tu-pagina" aria-label="LinkedIn" class="icon">
          {{-- LinkedIn --}}
          <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="3" fill="none" stroke="currentColor" stroke-width="1.6"/>
            <rect x="6.1" y="9.8" width="2.6" height="8.1" fill="currentColor"/>
            <circle cx="7.4" cy="7.4" r="1.4" fill="currentColor"/>
            <path d="M12 17.9v-4.6c0-1.6 1.2-2.9 2.9-2.9s3 1.1 3 3v4.5h-2.6v-4.1c0-.9-.6-1.5-1.4-1.5s-1.4.6-1.4 1.5v4.1H12Z" fill="currentColor"/>
          </svg>
        </a>

        <a href="https://x.com/tu-pagina" aria-label="X (Twitter)" class="icon">
          {{-- X --}}
          <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
            <path d="M4 4l16 16M20 4L4 20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
          </svg>
        </a>

        <a href="https://www.youtube.com/@tu-canal" aria-label="YouTube" class="icon">
          {{-- YouTube --}}
          <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
            <rect x="2.5" y="6.5" width="19" height="11" rx="3" fill="none" stroke="currentColor" stroke-width="1.6"/>
            <path d="M11 10l4.8 2-4.8 2V10z" fill="currentColor"/>
          </svg>
        </a>

        <a href="https://wa.me/0000000000" aria-label="WhatsApp" class="icon">
          {{-- WhatsApp --}}
          <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
            <path d="M7 19l-2.4.7.7-2.3A8 8 0 1119.2 16 8 8 0 017 19Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            <path d="M9.6 8.9c.2-.5.6-.7 1-.5l1.1.5c.3.2.4.6.3 1l-.2.6c-.1.3 0 .6.3.9l1.1 1.1c.3.3.6.4.9.3l.6-.2c.4-.1.8 0 1 .3l.5 1.1c.2.4 0 .8-.5 1-1.2.5-2.7.3-4.1-.9-1.5-1.2-2.4-2.6-2.7-3.9-.1-.5 0-.9.2-1.3Z" fill="currentColor"/>
          </svg>
        </a>
      </div>

    </div>

    <div class="footer__bottom">
      <p>© {{ date('Y') }} Golden Street Moving Company. All rights reserved.</p>
      <p>Developed by <a href="https://severalreves.com" target="_blank">Severalreves</a></p>
    </div>
  </div>
</footer>