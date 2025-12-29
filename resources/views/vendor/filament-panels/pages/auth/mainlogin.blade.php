

<div id="loginform">
    @assets
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.10.1/lottie.min.js"></script>



    <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');
            
            * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'poppins',sans-serif;
            }
            
            #loginform  {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            }
            
            section {
                position: relative;
                max-width: 400px;
                background-color: #00000040;
                border: 2px solid rgba(255, 255, 255, 0.5);
                border-radius: 20px;
                backdrop-filter: blur(4px);
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 2rem 3rem;
            }
            
            h1 {
                font-size: 2rem;
                color: #fff;
                text-align: center;
            }
            
            .inputbox {
                position: relative;
                margin: 30px 0;
                max-width: 310px;
                border-bottom: 2px solid #fff;
            }
            
            .inputbox label {
                position: absolute;
                top: 50%;
                left: 5px;
                transform: translateY(-50%);
                color: #fff;
                font-size: 1rem;
                pointer-events: none;
                transition: all 0.5s ease-in-out;
            }
            
            input:focus ~ label, 
            input:valid ~ label {
                top: -5px;
            }
            
            .inputbox input {
                width: 100%;
                height: 60px;
                background: transparent;
                border: none;
                outline: none;
                font-size: 1rem;
                padding: 0 35px 0 5px;
                color: #fff;
            }
            
            .inputbox ion-icon {
                position: absolute;
                right: 8px;
                color: #fff;
                font-size: 1.2rem;
                top: 20px;
            }
            
            .forget {
                margin: 35px 0;
                font-size: 0.85rem;
                color: #fff;
                display: flex;
                justify-content: space-between;
            
            }
            
            .forget label {
                display: flex;
                align-items: center;
            }
            
            .forget label input {
                margin-right: 3px;
            }
            
            .forget a {
                color: #fff;
                text-decoration: none;
                font-weight: 600;
            }
            
            .forget a:hover {
                text-decoration: underline;
            }
            
            button {
                width: 100%;
                height: 40px;
                border-radius: 40px;
              
                border: none;
                outline: none;
                cursor: pointer;
                font-size: 1rem;
                font-weight: 600;
                transition: all 0.4s ease;
            }
            
            button:hover {
            background-color: rgb(255, 255,255, 0.5);
            }



           .typing {
            /* font-family: 'Courier New', Courier, monospace; */
      font-size: 2.5rem;
      color: #080808a2;
      white-space: nowrap;
      overflow: hidden;
      border-left: 45px solid;
      width: 100%; /* Control the width of the text being typed */
      animation: typing 8s steps(24) 1s forwards, blink 500ms step-end infinite;
         }
        /* Typing effect */
        @keyframes typing {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
            }

            /* Cursor blinking effect */
            @keyframes blink {
            from {
                border-color: transparent;
            }
            to {
                border-color: black;
            }
            }
        
    </style>
 @endassets

 <div id="lottie-animation" class="hidden sm:block" style="width: 300px; height: 300px;position: absolute;left:18%;top:2%;z-index:10;"></div>

 @script
 <script>
     let baseurl = "{{ url('/') }}";
    //  console.log("baseurl",baseurl+'/assets/Animation - 1725940025355.json')
     let animation = lottie.loadAnimation({
     container: document.getElementById('lottie-animation'), // the container to render the animation
     renderer: 'svg', // use 'svg', 'canvas', or 'html'
     loop: true, // make it loop
     autoplay: true, // start automatically
     path: baseurl+'/assets/Animation - 1725940025355.json' // the path to the animation JSON file
 });
 </script>
 @endscript

 <div class="flex flex-col">
     <h1 class="typing">{{ env('APP_NAME') }}</h1>
     <section>
         <x-filament-panels::form id="form" wire:submit="authenticate">
             {{ $this->form }}
     
             <x-filament-panels::form.actions
                 :actions="$this->getCachedFormActions()"
                 :full-width="$this->hasFullWidthFormActions()"
             />

             <div>
                Don't have an account yet? <br><a class="text-primary-500" wire:navigate href="{{ filament()->getRegistrationUrl() }}">Register Now!</a>
             </div>
         </x-filament-panels::form>

       
 
     </section>

 </div>
</div>