$path='c:\xampp\htdocs\Live-News-Portal-in-PHP-main\Live-News-Portal-in-PHP-main\includes\sidebar.php'
(Get-Content $path) -replace 'src="images/ads.jpg"','src="C:\Users\ongno\Downloads\a-2048x647.jpg"' -replace 'src="images/ad-banner.jpg"','src="C:\Users\ongno\Downloads\a-2048x647.jpg"' | Set-Content $path
