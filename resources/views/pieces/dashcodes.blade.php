<script>
    // intent mgt
        function handle_intent() {
            // requires UI to have an intent map setup
            if(window['intent_map'] == undefined){
                return;
            }

            let intent = getpage_intent();
            intent = (intent == undefined) ? 'default' : intent;
            intent_map[intent]();
        }
        function getpage_intent() {
            let link = window.location.href;
            let intent = link.split('#');

            if(intent.length < 2){
                return undefined;
            }

            return intent[1];
        }
</script>
