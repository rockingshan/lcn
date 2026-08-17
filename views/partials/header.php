<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'LCN Management'; ?></title>
    
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo BASE_PATH; ?>/images/android-icon-192x192.png">
    
    <!-- Using Tailwind CSS via CDN for development -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>
<body class="bg-gray-100 text-gray-800 pb-16">

<nav class="bg-white/30 backdrop-blur-lg border-b border-gray-200 shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Left Side -->
            <div class="flex items-start space-x-4">
                <!-- Logo -->
                <a href="<?php echo BASE_PATH; ?>/" class="flex items-center py-5 px-2 text-gray-700 hover:text-gray-900">
                    <span class="font-bold">Meghbela LCN</span>
                </a>
                <!-- Primary Nav -->
                <div class="hidden md:flex items-center space-x-1">
                    <!--<div class="relative group">
                         <a href="#" class="py-5 px-3 text-gray-700 hover:text-gray-900">Downloads <i class="fas fa-chevron-down fa-xs"></i></a>
                         <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-0 pt-2 w-48 z-10">
                            <a href="<?php echo BASE_PATH; ?>/export-lcn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Download LCN</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Download Master LCN</a>
                         </div>
                    </div>-->
                    <?php if (\App\Access::can('ird')): ?><a href="<?php echo BASE_PATH; ?>/ird-inventory" class="py-5 px-3 text-gray-700 hover:text-gray-900">Broadcaster IRD Inventory</a><a href="<?php echo BASE_PATH; ?>/ird-challan" class="py-5 px-3 text-gray-700 hover:text-gray-900">IRD Challan Details</a><?php endif; ?>
                    <?php if (\App\Access::can('logs')): ?><a href="<?php echo BASE_PATH; ?>/logs" class="py-5 px-3 text-gray-700 hover:text-gray-900">View Logs</a><?php endif; ?>
                    <?php if (\App\Access::can('generator')): ?><a href="<?php echo BASE_PATH; ?>/lcn-strings" class="py-5 px-3 text-gray-700 hover:text-gray-900">LCN Strings<?php if (!empty($_SESSION['changed_frequencies'])): ?> <span class="bg-red-600 text-white rounded-full px-2 text-xs"><?php echo count($_SESSION['changed_frequencies']); ?></span><?php endif; ?></a><?php endif; ?>
                    <?php if (!empty($_SESSION['is_admin'])): ?><a href="<?php echo BASE_PATH; ?>/users" class="py-5 px-3 text-gray-700 hover:text-gray-900">Users</a><?php endif; ?>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden md:flex items-center space-x-3">
                 <div class="relative group">
                    <form id="cityForm" action="<?php echo BASE_PATH; ?>/set-city" method="POST">
                        <input type="hidden" name="city_id" id="city_id_input" value="<?php echo isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1; ?>">
                        <a href="#" class="py-5 px-3 text-gray-700 hover:text-gray-900 flex items-center" onclick="event.preventDefault(); document.getElementById('cityDropdown').classList.toggle('hidden');">
                            <i class="fas fa-plane mr-1"></i> 
                            <?php 
                                $cityNames = [1=>'Kolkata',2=>'Chandipur',3=>'Berhampore',4=>'SITI Headend'];
                                $currentCity = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
                                echo $cityNames[$currentCity];
                            ?>
                            <i class="fas fa-chevron-down fa-xs ml-1"></i>
                        </a>
                        <div id="cityDropdown" class="absolute right-0 hidden bg-white shadow-lg rounded-md mt-0 pt-2 w-48 z-10">
                            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="setCity(1)">Kolkata</button>
                            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="setCity(2)">Chandipur</button>
                            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="setCity(3)">Berhampore</button>
                            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="setCity(4)">SITI Headend</button>
                        </div>
                    </form>
                </div>
                <?php if (\App\Access::can('add_records')): ?><div class="relative group">
                    <a href="#" class="py-5 px-3 text-gray-700 hover:text-gray-900 flex items-center">
                        <i class="fas fa-plus mr-1"></i> Add Records <i class="fas fa-chevron-down fa-xs ml-1"></i>
                    </a>
                    <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-0 pt-2 w-48 z-10">
                        <a href="<?php echo BASE_PATH; ?>/ird-inventory/add" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add New IRD</a>
                        <a href="<?php echo BASE_PATH; ?>/add-sid" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add New SID</a>
                        <a href="<?php echo BASE_PATH; ?>/add-channel-mapping" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add Channel Mapping</a>
                        <a href="<?php echo BASE_PATH; ?>/add-channel" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add New Channel</a>
                    </div>
                </div><?php endif; ?>
                <a href="<?php echo BASE_PATH; ?>/logout" class="py-2 px-3 bg-red-500 text-white rounded hover:bg-red-600 transition duration-300 flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>

            <!-- Mobile button -->
            <div class="md:hidden flex items-center">
                <button class="mobile-menu-button p-2 rounded-md hover:bg-gray-100">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu hidden md:hidden">
        <!--<a href="#" class="block py-2 px-4 text-sm hover:bg-gray-200">Downloads</a> -->
        <a href="#" class="block py-2 px-4 text-sm hover:bg-gray-200">Broadcaster IRD Inventory</a>
        <a href="#" class="block py-2 px-4 text-sm hover:bg-gray-200">Modification Logs</a>
        <hr/>
        <a href="#" class="block py-2 px-4 text-sm hover:bg-gray-200">Change City</a>
        <a href="<?php echo BASE_PATH; ?>/logout" class="block py-2 px-4 text-sm hover:bg-gray-200">Logout</a>
    </div>
</nav>

<main class="p-6"> 
<script>
function setCity(cityId) {
    document.getElementById('city_id_input').value = cityId;
    document.getElementById('cityForm').submit();
}
</script>
