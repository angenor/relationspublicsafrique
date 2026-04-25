
    <div class="col-lg-6 col-xl-6">
        <form  wire:submit.prevent="submit" class="form-style5 ajax-contact">
            <div class="vs-circle"></div>
            <h3 class="form-title"> Nous contacter </h3>

            <div class="form-group">
                <input wire:model="name" type="text" name="name" id="name" placeholder="Nom et Prénom">
                <div class="text-danger" >@error('name') {{ $message }} @enderror</div>
            </div>
            <div class="form-group">
                <input wire:model="email" type="text" name="email" id="email" placeholder="Email">
                <div class="text-danger">@error('email') {{ $message }} @enderror</div>
            </div>
            <div class="form-group">
                <input wire:model="number" type="text" name="number" id="number" placeholder="Téléphone">
                <div class="text-danger">@error('number') {{ $message }} @enderror</div>
            </div>

            <div class="form-group">
                <textarea wire:model="message" name="message" id="message" placeholder="Message"></textarea>
                <div class="text-danger"> @error('message') {{ $message }} @enderror</div>
            </div>

            <button type="submit" class="vs-btn">Valider </button>

        </form>
</div>

