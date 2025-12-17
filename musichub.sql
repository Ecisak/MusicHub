-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 17, 2025 at 06:07 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `musichub`
--

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `genre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `genre`) VALUES
(10, 'Blues'),
(11, 'Country'),
(14, 'Elektronická hudba'),
(12, 'Folk'),
(13, 'Hip-hop'),
(6, 'Indie'),
(7, 'Jazz'),
(17, 'Klasická hudba'),
(8, 'Lo-fi'),
(4, 'Metal'),
(5, 'Metalcore'),
(3, 'Pop'),
(2, 'Punk'),
(16, 'R&B & soul'),
(9, 'Rap'),
(1, 'Rock'),
(15, 'Ska');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int(11) NOT NULL,
  `id_reviewer` int(11) NOT NULL,
  `id_song` int(11) NOT NULL,
  `rating_quality` tinyint(4) NOT NULL,
  `rating_originality` tinyint(4) NOT NULL,
  `rating_lyrics` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id_review`, `id_reviewer`, `id_song`, `rating_quality`, `rating_originality`, `rating_lyrics`, `comment`, `created_at`, `status`) VALUES
(1, 4, 4, 10, 10, 8, '<p>banger</p><p> </p>', '2025-12-15 12:52:19', 'approved'),
(4, 5, 5, 10, 10, 10, '<p>banger</p>', '2025-12-15 18:31:03', 'approved'),
(5, 4, 3, 3, 3, 3, '<p>nic moc</p>', '2025-12-15 18:54:33', 'approved'),
(6, 4, 11, 10, 8, 9, '<p></p><p><br /> <strong>Radiohead</strong> jsou kapela, která se nikdy nespokojila s tím, co už jednou fungovalo.<br /> Každé jejich období působí jako <em>vědomý krok do neznáma</em> – někdy nepohodlný, ale téměř vždy<br /> silný a nadčasový.<br /></p><p></p><h3>Proč Radiohead fungují</h3><br /><ul><li><br /> </li><li><strong>Experimenty bez ztráty emocí:</strong> i v elektronických a minimalistických polohách zůstává hudba<p>You said:<br />Radiohead let down<br />ChatGPT said:</p><p>Jasně — tady máš hodnocení skladby „Let Down“ od Radiohead, připravené rovnou pro CKEditor (HTML + formátování):</p><p></p><h2>Hodnocení: Radiohead – Let Down</h2><p></p><p><br /> <strong>Let Down</strong> patří k těm skladbám, které na první poslech můžou působit nenápadně,<br /> ale postupně se z nich stává <em>silný emoční zážitek</em>. Je to píseň o odcizení,<br /> každodenní únavě a tichém zoufalství, které se skrývá pod povrchem běžného dne.<br /></p><p></p><h3>Co dělá Let Down výjimečnou</h3><br /><ul><li><br /> </li><li><strong>Postupná gradace:</strong> skladba se pomalu rozvíjí a nenápadně buduje napětí,<br /> které vyvrcholí v emotivním závěru.<br /> </li><li><strong>Vrstvení kytar:</strong> jemné arpeggia vytvářejí pocit pohybu a lehké nejistoty,<br /> který perfektně ladí s tématem písně.<br /> </li><li><strong>Vokál Thoma Yorka:</strong> křehký, skoro unavený projev dodává textu autentičnost<br /> a zranitelnost.<br /></li></ul><p></p><h3>Možné slabiny</h3><br /><ul><li><br /> </li><li><strong>Nenápadnost:</strong> kdo čeká výrazný refrén nebo okamžitý hook, může mít pocit,<br /> že se skladba drží při zemi.<br /> </li><li><strong>Vyžaduje soustředění:</strong> Let Down funguje nejlépe při klidném poslechu,<br /> ne jako kulisa.<br /></li></ul><p></p><blockquote><br /> <p><br />   <em>Let Down</em> je písnička, která mluví potichu – ale o to víc zůstává v hlavě.<br /> </p><br /></blockquote><p></p><p><br /> <strong>Verdikt:</strong> Jedna z emočně nejsilnějších skladeb na <em>OK Computer</em>,<br /> která ukazuje, že Radiohead umí zasáhnout i bez velkých gest a hlasitých momentů.<br /></p><br /> </li></ul>', '2025-12-16 07:14:46', 'approved'),
(7, 4, 10, 7, 5, 8, '<p></p><p><br /> <strong>TIT FOR TAT</strong> je krátká, přímočará skladba, která stojí hlavně na<br /> <em>autentičnosti emocí</em> a syrovém vyznění. Nehraje si na nic složitého –<br /> místo toho funguje jako rychlá zpověď plná frustrace a zklamání.<br /></p><p></p><h3>Co na skladbě funguje</h3><br /><ul><li><br /> </li><li><strong>Upřímnost:</strong> text působí velmi osobně a snadno se s ním dá ztotožnit.<br /> </li><li><strong>Minimalistická produkce:</strong> jednoduchý beat a klavír dávají prostor vokálu.<br /> </li><li><strong>Vokální projev:</strong> lehce zlomený tón dodává písni křehkost a emoce.<br /></li></ul><p></p><h3>Možné slabiny</h3><br /><ul><li><br /> </li><li><strong>Krátká stopáž:</strong> skladba skončí dřív, než stihne výrazně gradovat.<br /> </li><li><strong>Jednoduchá struktura:</strong> pro někoho může působit až příliš přímočaře.<br /></li></ul><p></p><blockquote><br /> <p><br />   <em>TIT FOR TAT</em> je písnička, která neútočí hlasitostí, ale emocemi.<br /> </p><br /></blockquote><p></p><p><br /> <strong>Verdikt:</strong> Ideální skladba pro momenty, kdy chceš slyšet něco<br /> krátkého, upřímného a emočně otevřeného. Nezahltí, ale zanechá stopu.<br /></p>', '2025-12-16 07:15:56', 'approved'),
(8, 4, 9, 8, 5, 6, '<p></p><p><br /> <strong>Manchild</strong> je hravá, ironická popová skladba, která si bere na mušku<br /> <em>nezralé chování ve vztazích</em>. Sabrina Carpenter tady působí sebevědomě,<br /> s nadhledem a lehkou dávkou sarkasmu, díky čemuž je píseň zábavná i trefná zároveň.<br /></p><p></p><h3>Co na skladbě funguje</h3><br /><ul><li><br /> </li><li><strong>Text s humorem:</strong> jednoduché, ale chytré řádky, které si umí rýpnout<br /> bez zbytečné hořkosti.<br /> </li><li><strong>Chytlavý refrén:</strong> lehký, hravý a snadno zapamatovatelný.<br /> </li><li><strong>Produkce:</strong> čistý popový zvuk, který podporuje atmosféru a nepřebíjí vokál.<br /></li></ul><p></p><h3>Možné slabiny</h3><br /><ul><li><br /> </li><li><strong>Předvídatelnost:</strong> struktura písně nepřekvapí a drží se osvědčených popových postupů.<br /> </li><li><strong>Menší hloubka:</strong> skladba cílí spíš na zábavu než na silný emoční dopad.<br /></li></ul><p></p><blockquote><br /> <p><br />   <em>Manchild</em> je přesně ten typ písničky, který si pustíš, když chceš<br />   vypnout a pobavit se – a občas si u toho protočit oči.<br /> </p><br /></blockquote><p></p><p><br /> <strong>Verdikt:</strong> Lehký, sebevědomý pop s nadsázkou, který funguje hlavně<br /> díky charismatu interpretky a chytlavosti. Ideální track na playlist,<br /> ne nutně na hluboké rozjímání.<br /></p>', '2025-12-16 07:16:51', 'rejected'),
(9, 4, 8, 8, 6, 7, '<p></p><p><br /> <strong>DAISIES</strong> je jemná, introspektivní skladba, která stojí hlavně na<br /> <em>otevřenosti emocí</em> a klidné atmosféře. Justin Bieber tady působí dospěleji,<br /> civilněji a bez snahy o velká popová gesta.<br /></p><p></p><h3>Co na skladbě funguje</h3><br /><ul><li><br /> </li><li><strong>Atmosféra:</strong> minimalistická produkce nechává skladbu plynout<br /> přirozeně a nenuceně.<br /> </li><li><strong>Vokální projev:</strong> klidný, místy až zranitelný tón dodává písni<br /> upřímnost.<br /> </li><li><strong>Text:</strong> jednoduchý, ale osobní – funguje hlavně díky emocím,<br /> ne složitým metaforám.<br /></li></ul><p></p><h3>Možné slabiny</h3><br /><ul><li><br /> </li><li><strong>Nenápadnost:</strong> skladba nevyčnívá okamžitě a spíš se ti dostane<br /> pod kůži až po více posleších.<br /> </li><li><strong>Nižší dynamika:</strong> kdo čeká výrazný refrén, může být lehce zklamaný.<br /></li></ul><p></p><blockquote><br /> <p><br />   <em>DAISIES</em> je písnička, která neřve – mluví potichu a spoléhá na náladu.<br /> </p><br /></blockquote><p></p><p><br /> <strong>Verdikt:</strong> Klidný, citlivý track, který dobře funguje v tichých<br /> momentech nebo jako součást atmosférického playlistu. Nejde o hit na první<br /> dobrou, ale o skladbu, která si najde své místo časem.<br /></p>', '2025-12-16 07:17:48', 'approved'),
(10, 4, 7, 6, 6, 6, '<p></p><p><br /> <strong>The Fate of Ophelia</strong> je dramatická, emočně nabitá skladba,<br /> která kombinuje <em>post-hardcore agresi</em> s teatrální vyprávěcí atmosférou.<br /> Alesana tu staví na kontrastu mezi jemností a chaosem, což dává písni silný,<br /> až tragický náboj.<br /></p><p></p><h3>Co na skladbě funguje</h3><br /><ul><li><br /> </li><li><strong>Dynamické přechody:</strong> střídání screamů a melodických pasáží<br /> drží posluchače neustále ve střehu.<br /> </li><li><strong>Temná atmosféra:</strong> skladba působí jako hudební tragédie,<br /> která se postupně rozpadá před očima.<br /> </li><li><strong>Text a koncept:</strong> inspirace tragickým osudem Ofélie dodává<br /> písni hloubku a literární přesah.<br /></li></ul><p></p><h3>Možné slabiny</h3><br /><ul><li><br /> </li><li><strong>Chaos pro nováčky:</strong> pro někoho může být struktura příliš<br /> roztříštěná a náročná na první poslech.<br /> </li><li><strong>Emo přepálenost:</strong> emoce jsou vyhnané na maximum, což nemusí<br /> sednout každému.<br /></li></ul><p></p><blockquote><br /> <p><br />   <em>The Fate of Ophelia</em> zní jako divadelní tragédie převedená do kytar,<br />   screamů a zoufalství.<br /> </p><br /></blockquote><p></p><p><br /> <strong>Verdikt:</strong> Silná skladba pro fanoušky dramatického post-hardcoru,<br /> kteří ocení příběh, emoce a nekompromisní intenzitu. Není jednoduchá,<br /> ale odmění tě, když jí dáš čas.<br /></p>', '2025-12-16 07:19:11', 'pending'),
(11, 4, 15, 7, 7, 6, '<p></p><p><br /> <strong>Sparks</strong> je tichá, intimní skladba, která stojí na<br /> <em>minimalismu a zranitelnosti</em>. Coldplay tu odhazují velká gesta<br /> a soustředí se na jednoduchou melodii a jemný vokál, což dává písni<br /> velmi osobní a melancholickou atmosféru.<br /></p><p></p><h3>Co na skladbě funguje</h3><br /><ul><li><strong>Jednoduchost:</strong> žádná přehnaná produkce, jen kytara,<br /> hlas a emoce.<br /> </li><li><strong>Vokál Chrise Martina:</strong> klidný, lehce nejistý projev<br /> podtrhuje pocit lítosti a křehkosti.<br /> </li><li><strong>Nálada:</strong> ideální skladba pro tiché večery nebo chvíle,<br /> kdy chceš zpomalit.<br /></li></ul><p></p><h3>Možné slabiny</h3><br /><ul><li><strong>Nenápadnost:</strong> Sparks nevyčnívá okamžitě a může zapadnout<br /> mezi výraznějšími tracky.<br /> </li><li><strong>Velmi pomalé tempo:</strong> nehodí se pro aktivní poslech,<br /> spíš jako emoční kulisa.<br /></li></ul><p></p><blockquote><br /> <p><br />   <em>Sparks</em> je písnička, která tě osloví potichu – a právě proto funguje.<br /> </p><br /></blockquote><p></p><p><br /> <strong>Verdikt:</strong> Jeden z nejjemnějších momentů rané tvorby Coldplay,<br /> který ukazuje sílu jednoduchosti a upřímných emocí. Není okázalá,<br /> ale dlouhodobě zůstává v paměti.<br /></p>', '2025-12-16 07:28:10', 'approved'),
(12, 4, 14, 9, 8, 7, '<p></p><p><br /> <strong>Back to Friends</strong> je jemná, melancholická skladba, která zachycuje<br /> <em>bolestivý moment návratu do „normálu“ po vztahu</em>. Působí nenápadně,<br /> ale o to silněji zasahuje svou upřímností a tichým smutkem.<br /></p><p></p><h3>Co na skladbě funguje</h3><br /><ul><li><br /> </li><li><strong>Emoční autenticita:</strong> text působí přirozeně a nehraně,<br /> jako osobní zpověď bez přikrášlování.<br /> </li><li><strong>Minimalistická produkce:</strong> jednoduchý instrumentál nechává<br /> prostor emocím a náladě.<br /> </li><li><strong>Atmosféra:</strong> skladba má klidný, lehce zasněný vibe,<br /> který se snadno dostane pod kůži.<br /></li></ul><p></p><h3>Možné slabiny</h3><br /><ul><li><br /> </li><li><strong>Nízká dynamika:</strong> skladba se drží v jedné náladě<br /> a nevyhledává výrazné zvraty.<br /> </li><li><strong>Velmi tichý charakter:</strong> nehodí se na aktivní poslech,<br /> spíš na noční přemýšlení.<br /></li></ul><p></p><blockquote><br /> <p><br />   <em>Back to Friends</em> zní jako rozhovor, který nikdy neproběhl –<br />   klidný, ale emocionálně těžký.<br /> </p><br /></blockquote><p></p><p><br /> <strong>Verdikt:</strong> Nenápadná, ale silná skladba pro chvíle,<br /> kdy se snažíš smířit s tím, že některé věci už nejdou vrátit zpátky.<br /> Funguje hlavně díky náladě a upřímnosti.<br /></p>', '2025-12-16 07:29:04', 'approved'),
(13, 5, 15, 8, 6, 8, '<p>celkem fajn skladba</p>', '2025-12-16 07:32:12', 'approved'),
(14, 5, 14, 10, 10, 10, '<p>nejlepsi pisnicka</p>', '2025-12-16 07:32:41', 'approved'),
(15, 5, 11, 10, 10, 10, '<p>I love Radiohead. No, you don\'t understand. I love Radiohead. They are the best. Nothing is better than Radiohead. No, you don\'t understand. They are genius. Not just ok, genius. No, there are no other good bands. They are the best band. It makes me sad you don\'t understand that. Radiohead is more than a band. The Bends is entry level. I love Radiohead. I love them. King of Limbs will be genius. Everything they do is genius. I would die for them. You need to hear more of their music. I should tie you up in a room and make you listen to their entire catalog. Then you would understand how brilliant they are. You just don\'t understand them. I love Radiohead. They are my religion. If you don\'t love Radiohead, then I cannot understand you. They are great. I wish they were air so I could breathe them. I wish Radiohead was water so I could drink them and have them inside of me. All of their songs are amazing. All of them. I love Radiohead. They are more important than you. They are more important than any of us. You don\'t understand their greatness. I love Radiohead. I should kill you and stuff a stereo into your corpse so your body will sing Radiohead. I\'m just high on my love for all things Radiohead. I am not crazy, I just appreciate Radiohead. They are my favorite band. I have all of their albums. They are smart, they make people download. You just don\'t understand them. I wish I could take a bath in Radiohead. I would like to soak in all the greatness of their art. They are artists. Radiohead is more important than you or me. You just don\'t understand. I love them. You need to listen to Radiohead. You should listen to their new album. It will teach you things. I love Hail to the Thief. I love Thom Yorke. I love In Rainbows. Thom Yorke is the new religion.</p>', '2025-12-16 07:33:21', 'approved'),
(16, 5, 10, 4, 1, 3, '<p>nic moc</p>', '2025-12-16 07:33:37', 'approved'),
(17, 5, 9, 6, 8, 4, '<p>celkem fajn, jen ten text je nudny.</p>', '2025-12-16 07:34:08', 'approved'),
(18, 4, 17, 6, 6, 6, '<p>adwad</p>', '2025-12-16 10:49:56', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `id_song` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `cover_image` varchar(255) NOT NULL,
  `status` enum('pending','being_reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `author` varchar(100) NOT NULL,
  `id_genre` int(11) DEFAULT NULL,
  `release_year` int(4) DEFAULT NULL,
  `assigned_validator_id` int(11) DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `songs`
--

INSERT INTO `songs` (`id_song`, `id_user`, `title`, `filename`, `cover_image`, `status`, `uploaded_at`, `author`, `id_genre`, `release_year`, `assigned_validator_id`, `assigned_at`) VALUES
(3, 1, 'Elegy', 'music_691f267e6135f7.55808711.mp3', 'cover_691f267e6134c8.02061164.png', 'accepted', '2025-11-20 14:32:30', 'Architects', 5, 2025, NULL, NULL),
(4, 1, 'Elegy1', 'music_691f26dcb6ca79.77329483.mp3', 'cover_691f26dcb6c892.40439795.png', 'accepted', '2025-11-20 14:34:04', 'Architects', 5, 2025, NULL, NULL),
(5, 4, 'ElegyReviewer', 'music_691f27709f5ef2.24141206.mp3', 'cover_691f27709f5e61.40894640.png', 'accepted', '2025-11-20 14:36:32', 'Architects', 5, 2025, NULL, NULL),
(7, 7, 'The Fate of Ophelia', 'music_694064d4903265.25203539.mp3', 'cover_694064d4903128.49824887.png', 'accepted', '2025-12-15 19:43:16', 'Taylor Swify', 3, 2025, NULL, NULL),
(8, 7, 'DAISIES', 'music_69406540166a81.69505216.mp3', 'cover_694065401669d9.33613786.png', 'accepted', '2025-12-15 19:45:04', 'Justin Bieber', 3, 2025, NULL, NULL),
(9, 7, 'Manchild', 'music_694065913040a3.39409238.mp3', 'cover_69406591304026.89005885.png', 'accepted', '2025-12-15 19:46:25', 'Sabrina Carpenter', 3, 2025, NULL, NULL),
(10, 7, 'TIT FOR TAT', 'music_694065c279e807.09989976.mp3', 'cover_694065c279e778.41853724.png', 'accepted', '2025-12-15 19:47:14', 'Tate McRae', 3, 2025, NULL, NULL),
(11, 7, 'Let Down', 'music_69406608e92998.32100848.mp3', 'cover_69406608e92927.50515799.jpg', 'accepted', '2025-12-15 19:48:24', 'Radiohead', 1, 2025, NULL, NULL),
(13, 7, 'caw', 'music_69407268eac209.45168954.mp3', 'cover_69407268eac0d7.15097153.jpg', 'rejected', '2025-12-15 20:41:12', 'caw', 10, 2025, NULL, NULL),
(14, 7, 'back to friends', 'music_694108c72662b7.42458469.mp3', 'cover_694108c7266256.98090751.png', 'accepted', '2025-12-16 07:22:47', 'sombr', 3, 2025, NULL, NULL),
(15, 7, 'Sparks', 'music_694108f579d903.34438809.mp3', 'cover_694108f579d844.62679977.jpg', 'accepted', '2025-12-16 07:23:33', 'Coldplay', 3, 2025, NULL, NULL),
(16, 7, '12 to 12', 'music_69410928244034.64395540.mp3', 'cover_69410928243f41.63656115.jpg', 'accepted', '2025-12-16 07:24:24', 'sombr', 3, 2025, NULL, NULL),
(17, 7, 'wasd', 'music_694138c02b2105.35249148.mp3', 'cover_694138c02b1b13.88332608.jpg', 'accepted', '2025-12-16 10:47:28', 'dwad', 14, 2025, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('guest','author','reviewer','admin','banned','superadmin') NOT NULL DEFAULT 'guest',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Ecis', 'ecisedits@gmail.com', '$2y$10$WwP2H3n5RzejpLagOVFoHOVqnV8ZaOS8JAEfcgdcn4MMKCj.fwdSG', 'admin', '2025-10-02 14:34:32'),
(3, 'test', 'test@test.test', '$2y$10$UUxSdyiusxWcgCvXdlOg2OjKuS2sj6OrAGq1FUtkXHgoa/iOA16O.', 'reviewer', '2025-11-13 13:01:32'),
(4, 'reviewer1', 'reviewer@gmail.com', '$2y$10$hVzdng2Sw/wsHDjLGO7pCOqDB9K.NPdNcCkC/4bbT1ANESnoM4ye6', 'reviewer', '2025-11-20 14:34:43'),
(5, 'reviewer2', 'reviewer2@gmail.com', '$2y$10$EDsdaqZPHM6sT4FBV4kx8.J5Fv7A61iCJA9d3LPSV8NcpHGPEonPm', 'reviewer', '2025-11-20 14:37:10'),
(6, 'Superadmin1', 'superadmin@superadmin.superadmin', '$2y$10$wffYi7onIqR.SltsvtrtVe33sORmJ3cQbB5RV6TgVTwK7gtn2FBgW', 'superadmin', '2025-12-15 18:31:50'),
(7, 'User1', 'User1@User1.User1', '$2y$10$.ilw1U0ddaSZAUvtLzn0t.dpalInHp0K97Fc2DcHcQMIfbyf8pFy6', 'author', '2025-12-15 19:36:00'),
(9, 'Dejmiban1', 'dejmiban@dejmiban.dejmiban', '$2y$10$y07YbYhiN0evMNZDQbt9juHE5GjYjlGYN8pGczSgCRZWE6XLLWEBe', 'banned', '2025-12-16 07:36:16'),
(10, 'Dalsiadmin1', 'Dalsiadmin@gmail.com', '$2y$10$zmREb1pgMQIz2/egKKXpuerwJVawMQzAvtvzC0VXAduulZws1vrm.', 'admin', '2025-12-16 07:38:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `genre` (`genre`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD UNIQUE KEY `unique_user_song` (`id_reviewer`,`id_song`),
  ADD KEY `id_song` (`id_song`);

--
-- Indexes for table `songs`
--
ALTER TABLE `songs`
  ADD PRIMARY KEY (`id_song`),
  ADD UNIQUE KEY `filename` (`filename`),
  ADD UNIQUE KEY `cover_image` (`cover_image`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `idx_validator_id` (`assigned_validator_id`),
  ADD KEY `id_genre` (`id_genre`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `songs`
--
ALTER TABLE `songs`
  MODIFY `id_song` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`id_reviewer`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`id_song`) REFERENCES `songs` (`id_song`);

--
-- Constraints for table `songs`
--
ALTER TABLE `songs`
  ADD CONSTRAINT `fk_assigned_validator` FOREIGN KEY (`assigned_validator_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `songs_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `songs_ibfk_2` FOREIGN KEY (`id_genre`) REFERENCES `genres` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
