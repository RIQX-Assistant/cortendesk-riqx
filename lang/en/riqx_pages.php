<?php

/*
 * RIQX: text of the public /privacy and /Help pages. Same shape as lang/fa/riqx_pages.php.
 */
return [
    'ui' => [
        'contents' => 'On this page',
        'privacy' => 'Privacy',
        'help' => 'Help',
        'downloads' => 'Get the app',
        'site' => 'RIQX website',
        'updated' => 'Last updated: November 2026',
        'skip' => 'Skip to the text',
        'to_top' => 'Back to the top',
        'dark' => 'Dark theme; click for light',
        'light' => 'Light theme; click for dark',
        'auto' => 'Auto',
        'auto_title' => 'Auto: light from sunrise to sunset in Tehran',
        'tag' => 'Remote access, safe and simple',
        'about' => 'RIQX connects knowledge, tools and the research community. These pages cover the RIQX remote access app and its console.',
        'contact' => 'Reach us',
        'links' => 'Links',
        'address_label' => 'Address',
        'address' => 'Fourth floor, Lotus Building, Baghmisheh Junction, Ardabil, Iran',
        'postal_label' => 'Postal code',
        'phone_label' => 'Phone',
        'rights' => 'All rights reserved by RIQX.',
        'year' => '© 2026',
        'eitaa' => 'Eitaa',
        'telegram' => 'Telegram',
        'instagram' => 'Instagram',
        'meaning' => ['Research', 'International', 'Quality', 'Expertise'],
    ],

    'privacy' => [
        'title' => 'Privacy',
        'eyebrow' => 'PRIVACY',
        'heading' => 'Your data, in plain words',
        'intro' => 'What the RIQX app and console record, what they never keep, and how long anything stays.',
        'highlights' => [
            ['icon' => 'ri-eye-off-line', 'c' => '#4fa3ff', 'title' => 'Session content is not stored', 'text' => 'Screen, sound, keystrokes and files do not stay on our servers.'],
            ['icon' => 'ri-lock-2-line', 'c' => '#9b8cff', 'title' => 'Encrypted connections', 'text' => 'The relay only passes encrypted packets from one device to the other.'],
            ['icon' => 'ri-forbid-2-line', 'c' => '#2fd4a8', 'title' => 'No data sales', 'text' => 'We do not sell data, and there are no advertising trackers.'],
            ['icon' => 'ri-calendar-schedule-line', 'c' => '#ffb547', 'title' => 'Logs clear themselves', 'text' => 'Connection and sign-in logs are deleted automatically after 365 days.'],
        ],
        'sections' => [
            ['id' => 'scope', 'icon' => 'ri-compass-3-line', 'c' => '#4fa3ff', 'title' => 'What this covers', 'body' => [
                'This statement covers the RIQX remote access app (Windows, Android and macOS) and its console, and nothing else. Other RIQX services, such as AI or events, have their own terms.',
                'Only people with an account and an approved device can use the connection infrastructure, so part of the data is simply what we need to recognise that account and device.',
            ]],
            ['id' => 'data', 'icon' => 'ri-database-2-line', 'c' => '#9b8cff', 'title' => 'What gets recorded', 'body' => [
                ['ul' => [
                    '<b>Account:</b> username, name, email (if entered), role, and your language and theme choices. Passwords are kept only as hashes.',
                    '<b>Device:</b> device ID and public key, device name, operating system user name, operating system, processor, memory, app version, last time online, and public and local IP addresses.',
                    '<b>Connection log:</b> the ID and display name of both sides, IP address, connection type, start and end time, and any note you add to the session.',
                    '<b>File transfer log:</b> file paths and names, direction and count. The files themselves are not stored on the server.',
                    '<b>Sign-in and admin log:</b> successful and failed sign-ins with IP address and device type, and what administrators do in the console.',
                    '<b>Address book:</b> if you use it, the IDs, names and tags you save. A password kept there is encrypted.',
                    '<b>Web server log:</b> IP address, time and address of requests, for security and troubleshooting.',
                    '<b>Cookies:</b> only for the console session and sign-in, language and theme. We use no advertising cookies.',
                ]],
            ]],
            ['id' => 'not-stored', 'icon' => 'ri-shield-keyhole-line', 'c' => '#2fd4a8', 'title' => 'What we do not keep', 'body' => [
                'Screen images, sound, mouse movement and keystrokes, clipboard content and file content are not stored on our servers. The connection between two devices is encrypted, and when it passes through the relay, the relay only forwards encrypted packets.',
                'If you turn on session recording in the app, the file is saved only on your own device. The app and console use no behaviour analytics or third-party trackers.',
            ]],
            ['id' => 'purpose', 'icon' => 'ri-focus-3-line', 'c' => '#ffb547', 'title' => 'What it is used for', 'body' => [
                ['ul' => [
                    'Connecting devices and finding a route between them.',
                    'Access control, so that only approved accounts and devices use the infrastructure.',
                    'Security and accountability: seeing who connected to which device, and when.',
                    'Support, troubleshooting and delivering new versions of the app.',
                ]],
            ]],
            ['id' => 'ads-updates', 'icon' => 'ri-megaphone-line', 'c' => '#4fa3ff', 'title' => 'Ads and updates', 'body' => [
                'The app fetches the list of ads and new versions from the RIQX server. These requests carry no device or account ID, individual views and clicks are not recorded, and we build no advertising profile. For installers, only the total download count is kept.',
                'If you click an ad, the advertiser\'s site opens in your browser, and that site\'s own terms apply from there.',
            ]],
            ['id' => 'sharing', 'icon' => 'ri-group-line', 'c' => '#9b8cff', 'title' => 'Who can see this data', 'body' => [
                'Console administrators see accounts, devices and logs, as far as their role allows. We do not sell your data or hand it to anyone for advertising.',
                'We share data with others only when a competent legal authority requires it under the law.',
            ]],
            ['id' => 'retention', 'icon' => 'ri-time-line', 'c' => '#2fd4a8', 'title' => 'Where it lives and for how long', 'body' => [
                ['ul' => [
                    'The servers are in Iran.',
                    'Connection, file transfer, sign-in and admin logs are deleted automatically after <b>365 days</b>.',
                    'Web server logs stay for about <b>14 days</b>.',
                    'Accounts and devices stay until they are deleted.',
                    'The server is backed up monthly and the copies are kept off the server. The last 60 copies are kept. Before each console update, a copy of the database is also made on the server itself. So deleted data can remain in a backup until these copies are dropped.',
                ]],
            ]],
            ['id' => 'security', 'icon' => 'ri-shield-check-line', 'c' => '#ffb547', 'title' => 'How we protect it', 'body' => [
                'The console is reachable only over HTTPS, with TLS 1.2 and 1.3. Passwords are stored as hashes, two-factor sign-in is available, and a new device cannot use the infrastructure until it is approved. Connecting to any device also requires that device\'s own password.',
                'Even so, no system on the internet is entirely without risk. Keep your device and account passwords to yourself, and only let people you know connect.',
            ]],
            ['id' => 'permissions', 'icon' => 'ri-smartphone-line', 'c' => '#4fa3ff', 'title' => 'Permissions the app asks for', 'body' => [
                'Screen capture and Accessibility on Android, and Screen Recording, Accessibility and Input Monitoring on macOS, let your device be viewed and controlled remotely. <a href=":help">Help</a> explains how to grant them, and you can withdraw them in your system settings at any time.',
            ]],
            ['id' => 'rights', 'icon' => 'ri-user-settings-line', 'c' => '#9b8cff', 'title' => 'Your rights', 'body' => [
                'You can ask what data we hold about you and request that it be corrected or deleted. Use the contact details at the bottom of this page or ask your organisation\'s administrator. Logs needed for security may stay until their retention period ends.',
            ]],
            ['id' => 'license', 'icon' => 'ri-scales-3-line', 'c' => '#2fd4a8', 'title' => 'Software license', 'body' => [
                'The RIQX app and console are built on open-source software and released under the AGPL-3.0 license. Installing the app means accepting the terms of that license. The source code is available: <a href=":source_app" target="_blank" rel="noopener noreferrer">app source</a> and <a href=":source_panel" target="_blank" rel="noopener noreferrer">console source</a>.',
            ]],
            ['id' => 'changes', 'icon' => 'ri-refresh-line', 'c' => '#ffb547', 'title' => 'Changes to this statement', 'body' => [
                'If the way we work changes, we update this page and the date at its top.',
            ]],
        ],
    ],

    'help' => [
        'title' => 'Help',
        'eyebrow' => 'HELP CENTER',
        'heading' => 'Setting up RIQX, step by step',
        'intro' => 'Installing the app, signing in, granting permissions and fixing common problems, all on one page.',
        'highlights' => [
            ['icon' => 'ri-download-cloud-2-line', 'c' => '#4fa3ff', 'title' => 'Install', 'text' => 'Get the Windows, Android or macOS version.', 'href' => '#install'],
            ['icon' => 'ri-user-follow-line', 'c' => '#9b8cff', 'title' => 'Sign in', 'text' => 'Finish your account and device approval.', 'href' => '#account'],
            ['icon' => 'ri-apple-line', 'c' => '#2fd4a8', 'title' => 'macOS permissions', 'text' => 'The three permissions macOS asks for.', 'href' => '#mac-permissions'],
            ['icon' => 'ri-question-line', 'c' => '#ffb547', 'title' => 'Troubleshooting', 'text' => 'Common errors and how to fix them.', 'href' => '#faq'],
        ],
        'sections' => [
            ['id' => 'install', 'icon' => 'ri-download-cloud-2-line', 'c' => '#4fa3ff', 'title' => 'Installing the app', 'body' => [
                'Get the latest version from the <a href=":downloads">downloads page</a>. The server is already set in the app, so there is nothing to enter.',
                ['h' => 'Windows'],
                ['ol' => [
                    'Run the MSI file and allow the install when Windows asks.',
                    'An older version is replaced, and your device ID and settings stay.',
                ]],
                ['h' => 'Android'],
                ['ol' => [
                    'Open the APK file on your phone. If asked, allow installs from that app (your browser or file manager).',
                    'Google Play Protect may suggest a scan, because the app did not come from Google Play. Tap "Scan app" and the install continues.',
                ]],
                ['h' => 'macOS'],
                ['ol' => [
                    'Get the version for your Mac (Apple Silicon or Intel), open the dmg file and drag RIQX to Applications.',
                    'The first time, right-click the app and choose Open, because it has no Apple signature. If macOS refuses, use Open Anyway in System Settings → Privacy & Security.',
                    'Then grant the <a href="#mac-permissions">macOS permissions</a>.',
                ]],
            ]],
            ['id' => 'account', 'icon' => 'ri-user-follow-line', 'c' => '#9b8cff', 'title' => 'Account and device approval', 'body' => [
                ['ol' => [
                    'Get an account from your administrator. Without one you cannot start a connection.',
                    'Open the app. Your device waits in the console for the administrator to approve it.',
                    'Once approved, sign in through Settings → Account → Login.',
                ]],
            ]],
            ['id' => 'connect', 'icon' => 'ri-links-line', 'c' => '#2fd4a8', 'title' => 'Connecting to a device', 'body' => [
                ['ol' => [
                    'Enter the ID of the target device in the app\'s main box and click Connect.',
                    'Enter that device\'s password, or wait for the other side to accept.',
                ]],
                ['note' => 'Only give your device ID and password to people you know. Anyone with both can see and control your device.'],
            ]],
            ['id' => 'mac-permissions', 'icon' => 'ri-apple-line', 'c' => '#ffb547', 'title' => 'macOS permissions', 'body' => [
                'These are needed for your Mac to be viewed and controlled remotely. The "Permissions" card on the app\'s home page takes you to each setting with its Configure button.',
                ['ol' => [
                    'Open System Settings and go to Privacy & Security.',
                    '<b>Screen Recording:</b> turn RIQX on, so the screen can be sent.',
                    '<b>Accessibility:</b> turn RIQX on, so the mouse and keyboard work remotely.',
                    '<b>Input Monitoring:</b> turn RIQX on, so keys are sent correctly.',
                    'If macOS asks, quit the app and open it again.',
                ]],
                ['note' => 'If RIQX is missing from a list, or is on but does not work, remove it with the minus button, add it again from Applications with the plus button, and reopen the app.'],
            ]],
            ['id' => 'android-permissions', 'icon' => 'ri-android-line', 'c' => '#4fa3ff', 'title' => 'Android permissions', 'body' => [
                'Connecting from your phone to other devices needs no special permission. Only if you want your phone to be viewed or controlled remotely:',
                ['ol' => [
                    'Turn the service on in the app with Start service, and allow screen capture.',
                    'For remote control, give RIQX the Accessibility permission.',
                    'Exclude the app from battery optimization, so the system does not close it.',
                ]],
            ]],
            ['id' => 'updates', 'icon' => 'ri-refresh-line', 'c' => '#9b8cff', 'title' => 'Updating the app', 'body' => [
                'When a new version is available, a strip appears at the top of the app window. Nothing installs until you click.',
                ['ul' => [
                    '<b>Windows:</b> the file is downloaded and checked, then Windows asks for permission. The app closes and reopens, and open sessions end.',
                    '<b>Android:</b> the file is downloaded and Android\'s own install screen opens.',
                    '<b>macOS:</b> the strip takes you to the download page.',
                ]],
            ]],
            ['id' => 'faq', 'icon' => 'ri-question-line', 'c' => '#2fd4a8', 'title' => 'Common questions and errors', 'body' => [
                ['qa' => [
                    ['What does "This device is not approved" mean?', 'Your device has not been approved in the console yet. Give your device ID to your administrator to approve it. Then sign in and try again.'],
                    ['I cannot start a connection, but others can connect to me.', 'You must be signed in to start a connection. Sign in through Settings → Account → Login. If you are signed in and it still fails, ask your administrator whether starting connections is enabled for your device.'],
                    ['I see "Failed to connect… Please try later".', 'The app could not reach the server. This usually comes from the network path, not your account. Try again in a few minutes, turn off any VPN or proxy, check your firewall, and try another network if you can.'],
                    ['The password is right, but it will not connect.', 'A temporary password may change after each session. Ask the other side for the current one, and check the ID as well.'],
                    ['The picture is slow or low quality.', 'Check the internet speed on both sides and close heavy programs. On a slow network, lower the image quality from the toolbar at the top of the session window.'],
                    ['Does my device ID change with an update?', 'No. Your ID and settings stay through updates.'],
                ]],
            ]],
            ['id' => 'privacy-link', 'icon' => 'ri-shield-keyhole-line', 'c' => '#ffb547', 'title' => 'Privacy', 'body' => [
                'To see what is recorded and how long it stays, read the <a href=":privacy">privacy statement</a>.',
            ]],
        ],
    ],
];
