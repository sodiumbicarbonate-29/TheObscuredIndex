-- Migration: Import data to match current database schema
-- All manhwas assigned to user_id 1

SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
TRUNCATE TABLE Reread_History;
TRUNCATE TABLE Secret_Manhwas;
TRUNCATE TABLE Secret_Shelf_Access;
TRUNCATE TABLE User_Reading_Status;
TRUNCATE TABLE Manhwas;
TRUNCATE TABLE Current_Users;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert Users
INSERT INTO Current_Users (user_id, username, email, password_hash, created_at) VALUES
(1, 'dinnesh29', 'nicolemanondo29@gmail.com', '$2y$10$10FkqFc/ZWL6lo36hRCgUeRnlPxK.NpiPEMrdblZLgEivelHvPJiS', '2025-05-26 18:08:50'),
(3, 'nicole_cm', 'dinneshmanondo@gmail.com', '$2y$10$e.zEOZw9YtYnzEzduYpQxug4971TIi64.vsyeDLajzpb1VcUGdb0.', '2025-05-27 01:00:01'),
(4, 'klyder', 'torcyklyne@gmail.com', '$2y$10$syZb9QUe/F06GRKsxBz8qOIoFRTQ.jgPZMiHf30b8x9OMkO5im9.i', '2025-05-27 17:44:55'),
(5, 'razelkaye', 'rzlkyrns@gmail.com', '$2y$10$qx5G5Orf64yIA14CDwwZ7en/jxcTa9g/Ic1Mc.BvprfCweoUVlqVq', '2025-05-27 18:05:25'),
(6, 'Kal', 'khalelboydcpareja@gmail.com', '$2y$10$5k6PYfL9rVgAMJpO/h/McefRxNKjSx8YIkpmufQMOdIVEGXMu61zS', '2025-05-27 22:27:44'),
(7, 'FootFungus12', '11500384@usc.edu.ph', '$2y$10$P77epmpMjcmuH/JG30c8ueI9mX3t2wPQKBFC2RFFe9ynP1E4cGCRm', '2025-05-29 08:54:56'),
(8, 'aneir', 'reinaventures@gmail.com', '$2y$10$pnNa8E6a2CnCfKFC8DkpQuFMg32lyMflFTTAGiZs.SWYc88EDkCTq', '2025-08-30 11:35:42'),
(9, 'justin', 'jstn@email.com', '$2y$10$4w2aNMoqN9Lix8tso0guNOhM7UMuvzrvYNZsoXLR5UMDQ.gDNQmLK', '2025-11-12 12:36:54'),
(10, 'mattematician', 'mattgoat@gmail.com', '$2y$10$pZHqDP/3qjW4Ptd5vegwuuCzemXymxF28aaqtW8DryIS1F8AGQo5.', '2025-11-12 14:00:57');


INSERT INTO Current_Users (user_id, username, email, password_hash, created_at) VALUES
(11, 'zsof', '22102596@usc.edu.ph', '$2y$10$WO/RhBZ6KKoYsek6mJsK2e.kIBRbEAdpCsweqP3p0hPGsBd2HDD7u', '2025-11-13 08:08:51'),
(12, 'rose', '23101000@usc.edu.ph', '$2y$10$KCNI1xb4jiXq78f1DUgKdezgCSBYwKLO2KiyPVm5tOPluoXWzsWcK', '2025-11-13 12:42:14'),
(13, 'ginabot', '07500972@usc.edu.ph', '$2y$10$ZnibmxOnEd.lnhH6lzZczO6a/j.5ZCW0cZQs78gKdZSx6h.uaa.Ue', '2025-11-13 13:01:39'),
(14, 'BIGDICKKID', '23100262@usc.edu.ph', '$2y$10$63rraqBfgGLhxKEqjtyALuT4nim8o0kOpyxZugIdkv7gEUlV9vjY2', '2025-11-13 13:13:51'),
(15, 'mrchooey', 'ronpatrickramas7@gmail.com', '$2y$10$89YH3OqygDOnWWBKDglgfuX2..1J2/iKOknllDu//zuNbg8oPVN7q', '2025-11-13 13:20:54'),
(16, 'ssss', '12345678@gmail.com', '$2y$10$jfTt/ioqc3ItMEyxtL0vU.xxobVZElqne0fmanmZT/KzWi3HavUdW', '2025-11-13 14:05:55'),
(17, 'test', 'test@gmail.com', '$2y$10$GOu4I7.02vQ8/nF27gwZF.tLjn8iOyWrI4UwNTvgLAfVs1LKvEoNq', '2025-11-14 13:46:26'),
(18, 'jared01', 'gamorajared@gmail.com', '$2y$10$ae.JVty7lZlVMb7a8.0.J.lX6ZWHSbq72J6EdXOv7OJIOR4IbQn7y', '2025-11-14 15:39:53'),
(19, 'username12334', 'concieciative143@yahoo.com', '$2y$10$SN7vjkGpiT57JHb4EXXzUepE6D0u.JvQbMcIY3MEcFKO1XNqQu76.', '2025-11-17 12:34:50'),
(20, 'loyloy', 'loyan13aloba@gmail.com', '$2y$10$mHNU8LB7bXoRSAG5AwiYRen7YsULhezUz9K5fMzhf7ZBya541deaG', '2025-11-17 12:43:06'),
(21, 'skyyy', '21101425@usc.edu.ph', '$2y$10$0YHAB02.Ew7vrnNBwseEvellbgfbshjySyjOnsM4ttgIQQEoUlkZm', '2025-11-18 12:43:04'),
(22, 'asdasd', 'asdasd@gmail.com', '$2y$10$m2BViC4WwBsAiIgrrVlQ6OvKpYA9vhLwYjvo8zZvB5HCj.FCf0bWq', '2025-11-18 14:34:39'),
(23, 'domilian', '22102038@usc.edu.ph', '$2y$10$gztZJ9diwKeHjOjq6sn91u1p/k8BCUh7CrWnJc0PZDxEzs.8IP4fW', '2025-11-20 12:16:43'),
(24, 'zev', 'twinkfemboy@gmail.com', '$2y$10$IcSHF/HDL7PAHt5lG5jO9OZdzf82NVh3DkUUJsTOWTfrvGflIoT8e', '2025-11-26 13:07:20'),
(25, 'simon', 'nigga@gmail.com', '$2y$10$up1irPxFOQ/QPKCc9X66Eez7OrGSGxGrThKGTtO/BWkDjyAP/qyIC', '2025-11-26 13:50:27'),
(26, '123', '123@gmail.com', '$2y$10$CXdaY313DvX66UNqmKfTTeKxbuAF8vQtzAoAjDPxSv60j5MbTTPxK', '2025-11-26 16:43:04'),
(27, 'hestia', '21102926@usc.edu.ph', '$2y$10$Q6fAqVH2SjUjqSqSe0BQAeI08q4O9O2QXzNjOpI/JUGaCoJzeAehK', '2025-11-27 09:50:32'),
(28, 'asdf', 'asdf@gmail.com', '$2y$10$atnKJ/tXjJSYicvMTkut2u2Jpyx1aW1YC3ey.Nu7w.J92DD.cMyS6', '2025-11-27 13:39:29'),
(29, 'bruh1234', 'bruh@gmail.com', '$2y$10$AuW89wdKLahrSRlscJqeTez87P8g8HJR7ps.BvanzbXVURJoIJZka', '2025-11-27 16:32:38'),
(30, 'bruh12345', 'bruh1@gmail.com', '$2y$10$ZInntQLHrstKRUjrF8kvG.VKeNxFzyZ2ElARZd7J7sPUDtNHiCBvG', '2025-11-27 16:33:24');


INSERT INTO Current_Users (user_id, username, email, password_hash, created_at) VALUES
(31, 'mello', 'antoinettecabahug@gmail.com', '$2y$10$mhphgn6jNScjE8QkZ6x/m.XySJASHRmEeK3NOhlfOiBXgj2K.jla6', '2025-12-01 13:16:22'),
(32, 'mayls', 's23104513@usc.edu.ph', '$2y$10$VxOnP5LxZ6MnXba14z45Vu74s7w3OpSMNM/sGnqwjsruVlv/.ppZy', '2025-12-02 09:31:21'),
(33, 'getfgadsfsdfs', 'asdfsadf@yahoo.coma', '$2y$10$MLGb7FdTSYU5abJmaKU1r.cF4wfyRyYvgjl8H7QZWRCAKXzieM4JC', '2026-01-22 14:19:16'),
(34, 'lolheight', 'loly@gmail.com', '$2y$10$Mal4qKmUqIJEJAB9FZNfP.XgaZzsfAwXZ/CPU71Q3zEBpBtrFLBpe', '2026-01-26 13:11:11'),
(35, 'nealveloso', 'nealveloso@gmail.com', '$2y$10$fpGV6j15.lNdNj9mIcugAuFp2dob3vRZZcPTIXxmvoJOrjLJVQTge', '2026-01-26 18:05:33'),
(36, 'qwe', 'q@gmail.com', '$2y$10$O7RQjne/IR1pD3GOAx/aEubbL9npHOghfkwH2aTff6GcAcDy12e..', '2026-01-27 15:54:53'),
(37, '1c9ei0mbvu', '1c9ei0mbvu@wnbaldwy.com', '$2y$10$JlnKr2Cyt5Kxsi8xmVHNM.WxdWNLcvcRgSDQfnkEz8gGuqg4b5oZW', '2026-02-03 13:26:39'),
(38, 'asdasd4123', 'asdas23d@gmail.com', '$2y$10$nny8GrQRUoEiMahrzRqrwey.jmgBZKxfQQbwVGN201wn.JLzE6CC2', '2026-02-16 16:25:45'),
(39, 'Caulleiyx', 'rey04.antonio14@gmail.com', '$2y$10$Q2vLJOeVf.pMMhTQm6WACeodoTYaHs0AFixu408ToKB2JMjzj5esG', '2026-02-20 13:47:11'),
(40, 'wqeqw', 'ssadsds@usc.edu.ph', '$2y$10$8gEAwHd.tsCdQzuToSAiQupZps6jn74rz06pe9hAskla5Vl7jBi3C', '2026-03-02 10:33:59'),
(41, 'ASD', 'asdfgh@gmail.com', '$2y$10$rYpEYHmhVeTs2ilKpOBICe7oh3/MTHoITUlpAxiZJkdl1PFCSLG12', '2026-03-04 12:24:58'),
(42, 'kaila', '23104019@usc.edu.ph', '$2y$10$OV6xJA0Oc9b8n6Xip.MUse2Jj5E.xxgIrbW5B7NX3njkd0B6qRAJO', '2026-03-10 19:59:37'),
(43, 'swash', 'joshufaber06@gmail.com', '$2y$10$Igj42aDjghxlt//p7Drc5.l3k7JpLdjULjoSQ4Yth6bsD1hhxXOw.', '2026-03-18 13:33:30'),
(44, 'baller9000', 'vanaxel@gmail.com', '$2y$10$CccBKdJnOz0HZlVuHRBlR.v3quF9WgtgSvyDNgxtCc2eMbAE/VHm2', '2026-04-07 10:29:23'),
(45, 'yesbabe', '123123@gmail.com', '$2y$10$.pzenA1EILfpz6JQLkBGxOumIzCwxrj8U0jLwNVEcvDUmMM31cCLu', '2026-04-14 09:54:47'),
(46, 'RedMoon', 'zeroskai08@gmail.com', '$2y$10$Jci1vDSm38mx9ZnXksG8.uVIyohVb2xgmQyBGe9skesT3ViLDmyLe', '2026-04-20 16:35:45'),
(47, '1234567890', '1234567890@gmail.com', '$2y$10$OF.wb7aVpdY8Dw78V2zb4Of03WgLbGIsmdHP7Zes58yJoe0YEPZoC', '2026-04-23 15:09:32'),
(48, 'rwar', 'rwar@gmail.com', '$2y$10$yp4TnF7lsy4u1pwZ//v0zes6DMLk8UqVVhm6dN8ryt.lptcf5PV3i', '2026-04-25 15:23:18');

-- Reset auto increment
ALTER TABLE Current_Users AUTO_INCREMENT = 49;


-- Insert Manhwas (all assigned to user_id 1)
INSERT INTO Manhwas (manhwa_id, user_id, title, genre, author, status, description, upload_date, cover_image, reading_link, is_private) VALUES
(5, 1, 'The Insatiable Man', 'BL', 'Lee Huchu(Story&Art)', 'Completed', '', '2025-05-26 23:04:39', 'uploads/covers/cover_5_1748277969.jpg', 'https://dto.to/title/99798-the-insatiable-man-mature', 0),
(6, 1, 'Perle', 'BL', 'Cherry Manju / Arthur(Art)', 'Completed', 'dont read again it you dont wanna ruin your life', '2025-05-26 23:07:16', 'uploads/covers/683483a4590a8.jpeg', 'https://bato.to/title/147694-perle-official', 0),
(7, 1, 'Love in Orbit', 'BL', 'LattePanda(Story&Art)', 'Completed', '', '2025-05-26 23:10:25', 'uploads/covers/cover_7_1748277872.png', 'https://bato.to/title/123115-love-in-orbit', 0),
(8, 1, 'Turning', 'BL', '6Cho / Nono(Art) / Kooyoo(Art)', 'Ongoing', '', '2025-05-26 23:14:49', 'uploads/covers/cover_8_1748277659.png', 'https://xbato.com/title/107800-turning-official', 0),
(9, 1, 'Low Tide in Twilight', 'BL', 'Euja(Story&Art)', 'Ongoing', 'Night by the sea / Waterside night / Night by the Water / Night of Waterside.', '2025-05-27 00:43:30', 'uploads/covers/68349a329cc56.jpg', 'https://battwo.com/title/187956-low-tide-in-twilight-side-story', 0),
(10, 1, 'Lucky Paradise', 'BL', 'Kangneuk', 'Completed', 'hell first before paradise', '2025-05-27 01:15:23', 'uploads/covers/6834a1ab3c83b.jpg', 'https://xbato.com/series/82185', 0),
(11, 1, 'Love on Hold', 'BL', 'Mallinflower / Dalmeong(Art)', 'Ongoing', '', '2025-05-27 01:22:42', 'uploads/covers/6834a3622b3cd.jpg', 'https://xbato.com/title/131416-love-on-hold-official', 0),
(13, 1, 'Roses And Champagne', 'BL', 'Zig', 'Completed', '', '2025-05-27 01:39:25', 'uploads/covers/6834a74d2f27a.jpg', 'https://bato.to/series/127131', 0),
(14, 1, 'I Swear Im Not a Scammer!', 'BL', 'TT(Story&Art) / Titi(Story&Art)', 'Ongoing', '', '2025-05-27 01:42:06', 'uploads/covers/6834a7eee0284.jpeg', 'https://mto.to/title/153389-i-swear-i-m-not-a-scammer', 0),
(15, 1, 'Eye of the Storm', 'BL', 'Namhae / Siwon(Art)', 'Ongoing', '', '2025-05-27 01:48:24', 'uploads/covers/6834a968864ab.webp', 'https://mangatoto.com/title/182993-eye-of-the-storm', 0);


INSERT INTO Manhwas (manhwa_id, user_id, title, genre, author, status, description, upload_date, cover_image, reading_link, is_private) VALUES
(16, 1, 'The Sacred Serpent\'s Seduction', 'BL', 'Dagom(Story&Art)', 'Ongoing', '', '2025-05-27 02:02:35', 'uploads/covers/cover_16_1748283242.jpeg', 'https://battwo.com/title/164347-the-sacred-serpent-s-seduction-official', 0),
(17, 1, 'Non-Refundable Alpha', 'BL', 'Oh Doyeon(Story&Art)', 'Completed', '', '2025-05-27 02:17:06', 'uploads/covers/6834b022792d3.jpeg', 'https://bato.to/title/136341-non-refundable-alpha', 0),
(18, 1, 'The Fox\'s Love Refresher', 'BL', 'Siho / Okffe A / BBODAM(Art)', 'Ongoing', '', '2025-05-27 02:21:45', 'uploads/covers/6834b1398382a.jpeg', 'https://mto.to/title/174400-the-fox-s-love-refresher', 0),
(19, 1, 'Live, Laugh, Love', 'BL', 'Toyu', 'Ongoing', '', '2025-05-27 02:28:13', 'uploads/covers/6834b2bd7ee63.png', 'https://xbato.com/series/179670', 0),
(20, 1, 'December', 'BL', 'SamK / Merig(Art)', 'Completed', '', '2025-05-27 02:29:48', 'uploads/covers/6834b31c3e436.jpeg', 'https://battwo.com/title/147518-december-official', 0),
(21, 1, 'Unrequited Love Rendezvous', 'BL', '23 (ii)', 'Ongoing', '', '2025-05-27 03:14:40', 'uploads/covers/6834bda0effd5.jpeg', 'https://vymanga.com/manga/unrequited-love-rendezvous', 0),
(22, 1, 'Regas', 'BL', 'Lee kkeut / Samk', 'Ongoing', '', '2025-05-27 03:37:52', 'uploads/covers/6834c310e7872.jpeg', 'https://bato.to/series/154436/regas-official', 0),
(23, 1, 'Punch Drunk Love', 'BL', 'Moscareto', 'Completed', '', '2025-05-27 03:42:22', 'uploads/covers/6834c41eb6011.jpeg', 'https://bato.to/series/107035/punch-drunk-love', 0),
(24, 1, 'Cherry Cake', 'BL', 'Chayen', 'Ongoing', '', '2025-05-27 12:36:26', 'uploads/covers/6835414ad6627.jpeg', 'https://mangatoto.com/series/176124', 0),
(25, 1, 'Guiding Hazard', 'BL', 'Sunsun', 'Ongoing', '', '2025-05-27 17:57:32', 'uploads/covers/68358c8c1c4ba.jpeg', 'https://bato.to/title/136611-guiding-hazard-official', 0);


INSERT INTO Manhwas (manhwa_id, user_id, title, genre, author, status, description, upload_date, cover_image, reading_link, is_private) VALUES
(26, 1, 'Reunion', 'BL', '2coin / Lee Coin / Deulsum(Art)', 'Ongoing', '', '2025-05-27 17:58:59', 'uploads/covers/68358ce338035.jpeg', 'https://mto.to/title/116599-reunion-official', 0),
(27, 1, 'Our Paradise', 'BL', 'greeneer(Story&Art)', 'Completed', 'Green Apple Paradise', '2025-05-27 18:00:17', 'uploads/covers/cover_27_1748340565.jpeg', 'https://xbato.com/title/141754-our-paradise', 0),
(28, 1, 'Give Me Some Attention', 'BL', 'Rab(Story&Art)', 'Completed', 'Please pay attention to me', '2025-05-27 18:17:32', 'uploads/covers/6835913c96c1e.jpeg', 'https://mto.to/title/167823-give-me-some-attention', 0),
(29, 1, 'Mission: Save the Hunter', 'BL', 'Sorim / Hyeon(Art)', 'Ongoing', '', '2025-05-27 20:32:03', 'uploads/covers/6835b0c37b307.png', 'https://bato.to/title/166771-mission-save-the-hunter-mature-official', 0),
(30, 1, 'Zero Day Attack', 'BL', 'Summer / Yucheital', 'Completed', '', '2025-05-27 20:34:50', 'uploads/covers/6835b16a931cd.jpeg', 'https://bato.to/title/127852-zero-day-attack', 0),
(31, 1, 'Sparkling Baby', 'BL', 'Zec(Story&Art)', 'Ongoing', '', '2025-05-27 20:36:21', 'uploads/covers/6835b1c5b5aaa.jpeg', 'https://mto.to/title/148786', 0),
(32, 1, 'Define the Relationship', 'BL', 'Flona', 'Completed', '', '2025-05-27 20:39:57', 'uploads/covers/6835b29da2ba6.jpg', 'https://dto.to/title/98359-define-the-relationship-official', 0),
(33, 1, 'Sugar Trap', 'BL', 'Hyun Jiha', 'Ongoing', '', '2025-05-27 20:41:35', 'uploads/covers/6835b2ffb9f85.jpeg', 'https://bato.to/title/163133-sugar-trap-official', 0),
(34, 1, 'Unromantic Romance', 'BL', 'Jeong Seokchan(Story&Art)', 'Completed', '', '2025-05-27 21:09:50', 'uploads/covers/6835b99e5d736.jpeg', 'https://bato.to/title/120286-unromantic-romance-official', 0),
(35, 1, 'Love For Sale', 'BL', 'Dal Hyeon Ji(Story&Art)', 'Completed', '', '2025-05-27 21:11:46', 'uploads/covers/6835ba126e02d.jpeg', 'https://comicpark.org/title/343517-en-love-for-sale', 0);


INSERT INTO Manhwas (manhwa_id, user_id, title, genre, author, status, description, upload_date, cover_image, reading_link, is_private) VALUES
(36, 1, 'Trash of the Count\'s Family', 'No Romance', '', 'Ongoing', 'Lout of Count\'s Family', '2025-05-27 21:31:17', 'uploads/covers/6835bea5d8948.jpg', 'https://ravenscans.com/manga/trash-of-the-counts-family/', 0),
(37, 1, 'I Was Immediately Mistaken for a Monster Genius Actor', 'No Romance', 'Jangtan/ Jungseong Manyeo', 'Ongoing', '', '2025-05-27 21:59:22', 'uploads/covers/6835c53a0e66d.jpg', 'https://mangadex.org/title/f1d82e50-4c28-4621-a470-8f107eddeccd', 0),
(38, 1, 'Blue lock', 'No Romance', 'Muneyuki Kaneshiro', 'Ongoing', 'Blue Lock follows Yoichi Isagi', '2025-05-27 22:40:32', 'uploads/covers/6835cee09e3a7.jpg', 'https://w20.blue-lock-manga.com/', 0),
(39, 1, 'Dear Zero', 'BL', 'Brothers Without A Tomorrow(Story&Art)', 'Completed', 'season 1 done. I sense tragic....', '2025-05-27 22:41:58', 'uploads/covers/6835cf365bffd.jpeg', 'https://bato.to/title/163024-dear-zero-official', 0),
(40, 1, 'Too Much Regeneration! Now the King Obsesses Over Me', 'Straight', 'Crane', 'Ongoing', '', '2025-05-27 23:40:08', 'uploads/covers/6835dcd860de9.jpg', 'https://xbato.com/series/187009', 0),
(41, 1, 'The Priest Dreaming Of A Dragon', 'BL', '六倍利 / 半宛夜朝(Art)', 'Completed', 'priest looking for someone to take his virginity', '2025-05-27 23:56:10', 'uploads/covers/6835e09ad1b78.jpeg', 'https://xbato.com/title/165013', 0),
(42, 1, 'Payback', 'BL', 'samk', 'Ongoing', '', '2025-05-28 00:21:55', 'uploads/covers/6835e6a397b97.jpeg', 'https://battwo.com/title/96900-payback-official', 0),
(43, 1, 'Opposites Attract', 'BL', 'Oryu(Story&Art)', 'Completed', '', '2025-05-28 00:24:16', 'uploads/covers/6835e730ea472.jpeg', 'https://mto.to/title/107792-opposites-attract', 0),
(44, 1, 'In the Private Room', 'BL', 'SEOBANG', 'Completed', '', '2025-05-29 01:18:59', 'uploads/covers/68374583a4329.jpeg', 'https://bato.to/title/95812-in-the-private-room', 0),
(45, 1, 'My One and Only Cat', 'BL', 'Sonyeon(Story&Art)', 'Completed', '', '2025-05-29 08:23:05', 'uploads/covers/6837a8e90352c.jpeg', 'https://bato.to/title/105307', 0);


INSERT INTO Manhwas (manhwa_id, user_id, title, genre, author, status, description, upload_date, cover_image, reading_link, is_private) VALUES
(46, 1, 'Love at First Fright', 'BL', 'Kouzaki palace / Nangjun', 'Ongoing', '', '2025-05-29 08:25:33', 'uploads/covers/6837a97d5adda.webp', 'https://xbato.com/series/183835', 0),
(47, 1, 'Third Ending', 'BL', 'Chovom', 'Completed', '', '2025-05-29 08:28:53', 'uploads/covers/6837aa45e9263.jpeg', 'https://xbato.com/series/97716', 0),
(48, 1, 'I Swear We\'re Just Friends', 'Straight', 'Guseulso, Jyahwa / Myeon(Art)', 'Ongoing', 'Friends Don\'t Act Like This', '2025-05-29 08:33:19', 'uploads/covers/6837ab4f3c928.jpeg', 'https://mto.to/title/159025', 0),
(49, 1, 'The Spirit of Muryeong', 'BL', 'Today Spring / mp (II)(Art)', 'Ongoing', 'The Spirit of Muryeong', '2025-05-29 08:47:13', 'uploads/covers/6837ae915172a.webp', 'https://bato.to/title/177477', 0),
(50, 1, 'Netkama PUNCH!!!', 'BL', 'CHIMA / Mijjo / gongben / bongyee', 'Ongoing', '', '2025-05-29 08:52:53', 'uploads/covers/6837afe5f2018.jpeg', 'https://xbato.com/title/123917-netkama-punch', 0),
(51, 1, 'Love Jinx', 'BL', 'Geonhan', 'Completed', '', '2025-05-29 08:55:21', 'uploads/covers/6837b079ca45d.jpeg', 'https://bato.to/series/91525', 0),
(52, 1, 'The First Night With the Duke', 'Straight', 'Teava / MSG(Art)', 'Completed', 'I Took the Male Lead\'s First Time', '2025-05-30 23:09:04', 'uploads/covers/6839ca0fa83d8.jpeg', 'https://xbato.com/title/141679-the-first-night-with-the-duke-official', 0),
(53, 1, 'Lock me Up, Duke!', 'Straight', 'Kisai entertainment', 'Ongoing', '', '2025-05-30 23:10:53', 'uploads/covers/6839ca7d32e34.webp', 'https://nyxscans.com/series/lock-me-up-duke', 0),
(54, 1, 'The Beginning After the End', 'No Romance', 'Turtleme', 'Ongoing', '', '2025-05-31 12:50:16', 'uploads/covers/683a8a880fe48.jpeg', 'https://bato.to/series/72261/the-beginning-after-the-end', 0),
(55, 1, 'Let\'s Hide My Little Brother', 'Straight', 'Chaejihoo', 'Ongoing', '', '2025-06-02 16:20:28', 'uploads/covers/683d5eccc791c.webp', 'https://bato.to/series/94664', 0);


INSERT INTO Manhwas (manhwa_id, user_id, title, genre, author, status, description, upload_date, cover_image, reading_link, is_private) VALUES
(56, 1, 'The S-Classes that I Raised', 'No Romance', '', 'Ongoing', '', '2025-06-05 23:22:53', 'uploads/covers/6841b64de140c.jpeg', 'https://w35.thes-classesthatiraised.com/', 0),
(57, 1, 'Romance, But Not Romantic', 'BL', '', 'Completed', '', '2025-06-06 11:15:07', 'uploads/covers/68425d3b9001c.jpeg', 'https://bato.to/series/148115/romance-but-not-romantic-team-hazama', 0),
(58, 1, '4 Weeks Lovers', 'BL', 'Maroron(Story&Art)', 'Ongoing', '', '2025-06-06 14:19:20', 'uploads/covers/68428868c85eb.jpeg', 'https://bato.to/title/103234-4-week-lovers-official-mature', 0),
(59, 1, 'HOME5', 'BL', '', 'Completed', '', '2025-06-09 01:37:35', 'uploads/covers/6845ca5f65054.jpeg', 'https://bato.to/series/170414', 0),
(60, 1, 'S-Class Hunter Doesn\'t Want to Be a Villainous Princess', 'Straight', '', 'Ongoing', '', '2025-06-13 21:07:22', 'uploads/covers/684c2289c6092.webp', 'https://xbato.com/title/149629', 0),
(61, 1, 'Boss, Bxtch, Baby!', 'BL', '', 'Completed', '', '2025-06-16 07:30:36', 'uploads/covers/684f579c0307f.webp', 'https://xbato.com/title/124446', 0),
(62, 1, 'The Mafia Nanny', 'Straight', '', 'Ongoing', 'read on webtoon', '2025-06-16 07:33:33', 'uploads/covers/684f584d4f442.jpeg', NULL, 0),
(63, 1, 'Bailin and Li Yun', 'BL', '', 'Ongoing', '', '2025-06-16 07:34:58', 'uploads/covers/684f58a297580.jpeg', NULL, 0),
(64, 1, 'Into the Light Once Again', 'Straight', '', 'Ongoing', '', '2025-06-16 07:43:12', 'uploads/covers/684f5a90e13b2.webp', 'https://xbato.com/title/91253-into-the-light-once-again-official', 0),
(65, 1, 'Surviving a Harem', 'Straight', 'Jeonboon', 'Completed', 'Jun Yu, a WBA lightweight champion', '2025-11-13 13:10:38', 'uploads/covers/6915684e35417.webp', 'https://xbato.com/title/185651', 0);

-- Reset auto increment
ALTER TABLE Manhwas AUTO_INCREMENT = 67;


-- Insert User Reading Status
INSERT INTO User_Reading_Status (user_id, manhwa_id, reading_status, start_reading_date, finish_reading_date, last_updated) VALUES
(1, 5, 'Done', NULL, NULL, '2025-05-27 00:46:09'),
(1, 6, 'Done', '2025-05-25', '2025-05-25', '2025-05-26 23:07:16'),
(1, 7, 'Done', NULL, NULL, '2025-05-27 00:44:32'),
(1, 8, 'Currently Reading', NULL, NULL, '2025-05-27 00:40:59'),
(1, 9, 'Currently Reading', NULL, NULL, '2025-05-27 01:23:42'),
(1, 10, 'Done', NULL, NULL, '2025-05-27 01:15:23'),
(1, 11, 'Currently Reading', NULL, NULL, '2025-05-27 01:22:42'),
(1, 13, 'Done', NULL, NULL, '2025-05-27 01:39:25'),
(1, 14, 'Plan to Read', NULL, NULL, '2025-05-27 01:42:30'),
(1, 15, 'Currently Reading', '2025-05-23', NULL, '2025-05-27 01:48:24'),
(1, 16, 'Currently Reading', '2025-05-25', NULL, '2025-05-27 02:23:52'),
(1, 17, 'Done', NULL, NULL, '2025-05-27 20:25:14'),
(1, 18, 'Currently Reading', NULL, NULL, '2025-05-27 02:21:45'),
(1, 19, 'Currently Reading', '2025-05-26', NULL, '2025-05-27 02:28:13'),
(1, 20, 'Done', NULL, NULL, '2025-05-27 20:45:25'),
(1, 21, 'Currently Reading', '2025-05-26', NULL, '2025-06-03 01:36:33'),
(1, 22, 'Currently Reading', NULL, NULL, '2025-05-28 20:09:57'),
(1, 23, 'Done', '2025-06-06', '2025-06-13', '2025-06-13 23:31:16'),
(1, 24, 'Currently Reading', '2025-05-27', NULL, '2025-05-28 20:11:07'),
(1, 25, 'Currently Reading', NULL, NULL, '2025-05-27 17:57:32');


INSERT INTO User_Reading_Status (user_id, manhwa_id, reading_status, start_reading_date, finish_reading_date, last_updated) VALUES
(1, 26, 'Currently Reading', NULL, NULL, '2025-05-27 17:58:59'),
(1, 27, 'Done', NULL, NULL, '2025-05-27 18:09:25'),
(1, 28, 'Done', NULL, NULL, '2025-05-27 22:11:00'),
(1, 29, 'Currently Reading', '2025-06-01', NULL, '2025-06-01 23:34:59'),
(1, 30, 'Done', NULL, NULL, '2025-05-27 20:42:42'),
(1, 31, 'Done', NULL, NULL, '2025-05-27 20:36:21'),
(1, 32, 'Done', '2025-08-30', '2025-08-30', '2025-08-30 11:45:57'),
(1, 33, 'Currently Reading', NULL, NULL, '2025-05-27 20:41:50'),
(1, 34, 'Plan to Read', NULL, NULL, '2025-05-27 21:09:50'),
(1, 35, 'Done', '2025-06-01', '2025-06-02', '2025-06-02 16:09:26'),
(1, 36, 'Currently Reading', NULL, NULL, '2025-05-27 21:31:17'),
(1, 37, 'Currently Reading', NULL, NULL, '2025-05-27 21:59:22'),
(1, 38, 'Done', '2025-05-20', '2025-05-27', '2025-05-27 22:47:55'),
(1, 39, 'Done', '2025-06-02', '2025-06-02', '2025-06-02 16:10:23'),
(1, 40, 'Plan to Read', NULL, NULL, '2025-05-27 23:40:08'),
(1, 41, 'Currently Reading', '2025-05-28', NULL, '2025-05-29 01:20:36'),
(1, 42, 'Currently Reading', NULL, NULL, '2025-05-28 00:21:55'),
(1, 43, 'Currently Reading', '2025-08-04', NULL, '2025-08-04 11:32:44'),
(1, 44, 'Plan to Read', NULL, NULL, '2025-05-29 01:18:59'),
(1, 45, 'Plan to Read', NULL, NULL, '2025-05-29 08:23:05');


INSERT INTO User_Reading_Status (user_id, manhwa_id, reading_status, start_reading_date, finish_reading_date, last_updated) VALUES
(1, 46, 'Currently Reading', '2025-05-31', NULL, '2025-05-31 00:51:36'),
(1, 47, 'Done', NULL, NULL, '2025-05-29 08:28:53'),
(1, 48, 'Done', NULL, NULL, '2025-05-29 08:33:19'),
(1, 49, 'Plan to Read', NULL, NULL, '2025-05-29 08:47:13'),
(1, 50, 'Plan to Read', NULL, NULL, '2025-05-29 08:52:53'),
(1, 51, 'Done', NULL, NULL, '2025-05-29 08:55:21'),
(1, 52, 'Done', NULL, NULL, '2025-05-30 23:09:04'),
(1, 53, 'Currently Reading', '2025-06-13', NULL, '2025-06-13 23:31:25'),
(1, 54, 'Currently Reading', NULL, NULL, '2025-05-31 12:50:16'),
(1, 55, 'Currently Reading', '2025-06-07', NULL, '2025-06-07 10:22:25'),
(1, 56, 'Currently Reading', NULL, NULL, '2025-06-05 23:22:53'),
(1, 57, 'Currently Reading', '2025-06-06', NULL, '2025-06-06 11:15:07'),
(1, 58, 'Done', '2025-06-06', '2025-06-06', '2025-06-06 22:27:58'),
(1, 59, 'Currently Reading', '2025-06-08', NULL, '2025-06-09 01:37:35'),
(1, 60, 'Currently Reading', '2025-06-13', NULL, '2025-06-13 21:07:22'),
(1, 61, 'Currently Reading', '2025-11-19', NULL, '2025-11-19 14:29:50'),
(1, 62, 'Currently Reading', '2025-06-14', NULL, '2025-06-16 07:33:33'),
(1, 63, 'Currently Reading', '2025-06-15', NULL, '2025-06-16 07:34:58'),
(1, 64, 'Plan to Read', NULL, NULL, '2025-06-16 07:43:12'),
(3, 6, 'Plan to Read', '2025-05-25', '2025-05-26', '2025-05-27 01:09:08'),
(3, 9, 'Plan to Read', NULL, NULL, '2025-05-27 01:06:47'),
(6, 38, 'Currently Reading', '2023-10-13', NULL, '2025-05-27 22:40:32'),
(13, 65, 'Plan to Read', NULL, '2025-11-13', '2025-11-13 13:10:38');

-- Insert Reread History
INSERT INTO Reread_History (reread_id, user_id, manhwa_id, start_date, finish_date, created_at) VALUES
(1, 1, 35, '2025-06-01', NULL, '2025-06-01 15:28:00'),
(2, 1, 39, '2025-06-02', NULL, '2025-06-02 08:10:04'),
(3, 1, 23, '2025-06-06', NULL, '2025-06-06 03:57:38'),
(4, 1, 43, '2025-08-04', NULL, '2025-08-04 03:32:44'),
(5, 1, 32, '2025-08-30', NULL, '2025-08-30 03:40:42');

ALTER TABLE Reread_History AUTO_INCREMENT = 6;

-- Insert Secret Shelf Access
INSERT INTO Secret_Shelf_Access (access_id, user_id, granted_date) VALUES
(1, 1, '2025-06-02 17:50:23'),
(2, 1, '2025-06-02 17:58:46'),
(3, 1, '2025-06-02 18:10:12'),
(4, 1, '2025-06-02 18:13:05'),
(5, 1, '2025-06-02 18:17:17'),
(6, 1, '2025-06-02 18:21:16'),
(7, 1, '2025-06-02 18:22:50'),
(8, 1, '2025-06-02 18:32:16'),
(9, 1, '2025-06-02 20:15:40');

ALTER TABLE Secret_Shelf_Access AUTO_INCREMENT = 10;

