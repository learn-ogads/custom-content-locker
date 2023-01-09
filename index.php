<?php
    require_once "utils/DotEnv.php";

    (new DotEnv(__DIR__ . "/.env"))->load();

    require_once "utils/Server.php";
    require_once "utils/Offers.php";
    require_once "utils/DatabaseHandler.php";
    require_once "utils/User.php";

    $uid = bin2hex(random_bytes(16));
    $USER_AGENT = Server::getUserAgent();
    $IP_ADDRESS = Server::getIpAddress();
    $AFF_SUB4 = $uid;

    $offers = new Offers(getenv("API_KEY"), $AFF_SUB4);
    $allOffers = $offers->fetchOffers();

    $isMobile = Offers::isMobileOrTablet();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Content Locker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
<div class="container mx-auto w-full px-8 my-10 max-w-[900px] mb-20">
    <div class="flex flex-col items-center justify-center w-full mb-10">
        <img src="assets/imgs/logo.png" alt="EazyStacks Logo" class="w-16 mb-4">
        <p class="text-white/80 text-lg text-center">Complete one offer to unlock access to your eBook!</p>
    </div>

    <div class="flex flex-col w-full gap-y-8">
        <?php foreach($allOffers as $offer): ?>
            <a href="/tracking/click.php?offer_id=<?=$offer->offerid;?>&aff_sub4=<?=$uid;?>&link=<?=urlencode($offer->link);?>" <?php if (!$isMobile) echo 'target="_blank"'; ?> class="flex w-full items-center gap-x-3 p-3 bg-gray-700 rounded-lg transition-all duration-150 hover:translate-x-2 shadow shadow-white/20">
                <div class="rounded-lg w-16 lg:w-20 overflow-hidden object-cover shrink-0">
                    <img src="<?=$offer->picture;?>" alt="object-cover w-full h-full">
                </div>
                <div>
                    <h2 class="text-white font-medium text-lg lg:text-xl"><?=$offer->name_short;?></h2>
                    <p class="text-white/80"><?=$offer->adcopy;?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-gray-900 flex items-center justify-center border-t-2 border-white/30 py-3">
        <div role="status">
            <svg class="inline mr-2 w-6 h-6 text-gray-700 animate-spin fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
            </svg>
            <span class="sr-only">Loading...</span>
        </div>
        <span class="text-white font-medium">Waiting For Completion</span>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const AFF_SUB4 = <?=json_encode($AFF_SUB4);?>;
        const RECHECK_TIME = <?=json_encode(getenv("RECHECK_TIME"));?>;

        const isComplete = async () => {

            const resp = await fetch(`/tracking/progress.php?aff_sub4=${AFF_SUB4}`)
            const json = await resp.json()

            if (json && json.complete) {
                window.location.replace(`/tracking/unlock.php?aff_sub4=${AFF_SUB4}`)
                return
            }

            setTimeout(isComplete, RECHECK_TIME) // This will re-run after the specified recheck time.
        }
        isComplete()

    })
</script>

</body>
</html>