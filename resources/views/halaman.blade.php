<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                display: flex;
                flex-direction: column;
            }

            p {
                margin: 0;
                padding-bottom: 1pt;
            }

            .text-head {
                font-family: Arial, Helvetica, sans-serif;
                font-weight: bold;
                text-transform: uppercase;
                text-align: center;
                padding: 0;
            }

            .text-1 {
                font-size: 12pt;
                margin: 0;
            }

            .text-2 {
                font-size: 14pt;
                letter-spacing: 5px;
                margin: 0;
            }

            .text-3 {
                font-size: 16pt;
                margin: 0;
            }

            .text-4 {
                font-size: 12pt;
                font-weight: normal;
                margin: 0;
                text-transform: none;
            }

            .text-5 {
                font-size: 14pt;
                margin: 0;
                letter-spacing: 5px;
            }

            .head-line {
                margin: 0;
                border-bottom: 4px solid;
            }

            .kode-pos {
                font-size: 12pt;
                margin: 0;
                text-align: right;
                padding-right: 80px;
            }

            .main {
                text-align: justify;
                font-size: 11pt;
                letter-spacing: 0.7px;
            }
            
            footer {
                position: fixed; 
                bottom: 0px; 
                left: 0px; 
                right: 0px;
                height: 50px; }
            
        </style>
    </head>

    <body>
        <div>
            <header>
                <table>
                    <tr>
                        <td><img src="{{asset('image001.jpg')}}" alt="kota kediri"/></td>
                        <td class="text-head">
                            <p class="text-1">pemerintah kota kediri</p>
                            <p class="text-2">dinas kesahatan</p>
                            <p class="text-3">rumah sakit umum daerah gambiran</p>
                            <p class="text-4">
                                Jl. Kapten Pierre Tendean No. 16 Telp. 0354-2810000, 2810001,
                                2810008
                            </p>
                            <p class="text-4">email:rsud.gambiran@kedirikota.go.id</p>
                            <p class="text-5">kediri</p>
                        </td>
                        <td> <img src="{{asset('image002.png')}}" alt="Rsud gambiran" class="img-top-right" /></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p class="kode-pos">Kode Pos : 64132</p>
                            <div class="head-line"></div>
                        <td>
                    </tr>
                </table>
            </header>
                @yield('main')
            <footer>
                <img src="{{asset('image003.png')}}" alt="footer" />
            </footer>
        </div>
    </body>

</html>