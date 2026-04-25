@extends('layouts.default')

@section('content')
    <div class="breadcumb-wrapper " data-bg-src="{{asset('front/front/assets/img/breadcumb/breadcumb-bg.png')}}">
    <div class="container z-index-common">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title"> Contact </h1>
            <div class="breadcumb-menu-wrap">
                <ul class="breadcumb-menu">
                    <li><a href="{{url('/')}}">Accueil</a></li>
                    <li> Contact</li>
                </ul>
            </div>
        </div>
    </div>
</div><!--==============================
  Contact Area
  ==============================-->
<section class=" space-top space-extra-bottom">
    <div class="container">
        <div class="row gx-80">
            <div class="col-lg-6 col-xl-6 mb-30 mb-lg-0">
                <h2 class="h1 mt-n2">Contactez nous</h2>

                <h3 class="border-title2 h5">Regional Office</h3>
                <p class="contact-info">
                    <i class="fas fa-clock"></i>
                    Office hours are 9am – 5pm <br> Monday-Thursday and 9am – 4.30pm on Friday.
                </p>
                <p class="contact-info">
                    <i class="fas fa-map-marker-alt"></i>
                    1309 Beacon Street, Suite 300, Brookline, MA, 02446
                </p>
                <p class="contact-info">
                    <i class="fas fa-phone-alt"></i>
                    <a class="text-inherit" href="tel:+11234562228">(00) 123 456 789</a>
                </p>
                <p class="contact-info">
                    <i class="fas fa-envelope"></i>
                    <a class="text-inherit" href="mailto:hello@domainname.com">hello@domainname.com</a>
                </p>
                <div class="mega-hover rounded-20 mt-4 mt-lg-5 mb-4"><img src="front/assets/img/about/contact-1.jpg" alt="office" class="w-100"></div>
                <p class="font-title text-title fs-md fw-medium pt-xl-2 mb-2">Membership enquiries: <a href="tel:+04432907612" class="text-decoration-underline">+0443-290 7612</a></p>
                <p class="font-title text-title fs-md fw-medium mb-4">Principal Support: <a href="tel:+2256366989" class="text-decoration-underline">+225636-6989</a></p>
            </div>
            <livewire:contactform />
        </div>
    </div>
</section>
<div class="overflow-hidden rounded-20 space-bottom">
    <div class="container">
        <iframe class="bdrs20" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d50437.32487690385!2d144.96230200000002!3d-37.805673!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sEnvato!5e0!3m2!1sen!2sbd!4v1677062621439!5m2!1sen!2sbd" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div><!-- FAQ Area -->


@endsection
