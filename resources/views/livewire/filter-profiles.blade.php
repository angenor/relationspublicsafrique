<div class="">
    <!--==============================
    About Area
==============================-->
    <section class=" space-bottom">
        <div class="container">

        </div>
    </section><!--==============================
      Team Area
  ==============================-->
    <section class="space-bottom">
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="title-area text-center">
                        <div class="sec-icon">
                            <div class="vs-circle"></div>
                        </div>
                        <span class="sec-subtitle">{{$pageTitle}}</span>
                        <h2 class="sec-title h1">{{$pageTitle}}  </h2>
                    </div>
                </div>


            </div>
            <div class="d-flex justify-content-center mb-2  ">
                <div class="col d-flex justify-content-center">

                    <label class="mr-2">
                        <input type="text" wire:model.live.debounce.500ms="name" placeholder="Nom" class="form-control w-full ">
                    </label>
                    <div style="width: 30px;" ></div>
                    <label>
                        <select wire:model.live="pays">
                            <option value="">Sélectionnez un pays</option>
                            @foreach($paysList as $pays)
                                <option value="{{ $pays->id }}">{{ $pays->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            <div class="row justify-content-center">




                @forelse($profils as $k=>$v)
                    <div class="col-6 col-sm-6 col-lg-4 col-xxl-3">

                        <div class="team-style2 has-border">
                            <div class="team-content">
                                <h5 class="team-name h5 text-wrap truncate" style="font-size: medium">
                                    <a href="{{route('profil.show',[$v->slug,$v->id])}}" wire:navigate> {{$v->fullname}}</a>
                                </h5>
                                <p class="team-degi">{{$v->fonction}}</p>

                                <div class="team-img">
                                    <a href="{{route('profil.show',[$v->slug,$v->id])}}" wire:navigate>
                                        <img src="{{$v->img}}" alt="member" id="profilimg">
                                    </a>
                                </div>

                                <p class="team-experi">{{$v->pays->name??'Non renseigné'}}</p>
                            </div>
                        </div>


                    </div>

                @empty
                    <div class="col-sm-6 col-lg-6 col-xxl-3">
                        <div class="team-style2 has-border">
                            <div class="team-content">
                                <h5 class="team-name h5 text-wrap truncate" style="font-size: medium">
                                    <a href="#">Aucun membre</a>
                                </h5>
                                <p class="team-degi">Aucun membre</p>
                            </div>
                        </div>
                    </div>


                @endforelse
                <div class="">
                    {{ $profils->links() }}
                </div>

            </div>

        </div>
    </section><!--==============================
      Testimonial Area
  ==============================-->
    <section class="overflow-hidden bgc-f6 space-top space-extra-bottom">
        <div class="shape-mockup jump d-none d-xxl-block" data-left="-14%">
            <div class="vs-border-circle"></div>
        </div>
        <div class="shape-mockup jump-img d-none d-xxl-block" data-right="-15%" data-top="170px">
            <div class="vs-circle color2"></div>
        </div>
        <div class="shape-mockup jump-img d-none d-xxl-block" data-left="8%" data-bottom="10%">
            <div class="shape-dotted style2"></div>
        </div>

    </section>
    <!--==============================
    Footer Area
    ==============================-->

</div>

<style>
/* Styles pour les images de profil carrées */
.team-img {
    width: 100%;
    height: 200px;
    overflow: hidden;
    border-radius: 10px;
}

.team-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}
</style>
