<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>错误页面</title>
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="stylesheet" href="/dist/layui/css/layui.css" />
    <style>
        body {
            background-color: #F0F2F5;
            padding: 15px;
        }

        .main {
            background-color: #FFFFFF;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            overflow: hidden;
            border-radius: 5px;
        }

        .icon-box {
            width: 72px;
            height: 72px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 100px;
            margin-bottom: 20px;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAQIklEQVR4Xu1deXQdZRX/3Xlp0tKUolRRNgsWUBGrWHbUKIWa5M17rVAFFw4oRw9qce1G6RLapi1YNgGhqCwethO1efPeS2iptIddbbWACBUKCLZHCii0KW3SZK7nvpm0EJPMPm/mZb5/epp3v7v87m+++fYhJGVII0BDOvokeCQEGOIkSAiQEGCIIzDEw09agIQAQxyBIR5+0gIkBKhcBDiTOQjAOOj0YRDGgnk0iEaCuRaEWjCNBDGDqQPEO8HoAFEHgDfBeBE6NqNGeZ5WrnyjUlGqmBaAVXU/6DQRhLMAnALgIyDaz5fEMW8H6BkQHgX33EeFwmpf9EZASawJwOn0UaBUFoxJIEwMEc9dAP8BjFVIUY5yuVdCtO2rqdgRgFX1cDB9DaAvg/BJX9Fwo4yZAXoc4Huhd99LbW3/dqOmXHViQQCeOnUEdu++CEwXgOj4coFlyy7zwyC6nfK5X9qSL7NQpAnAkycfgB6+BMzTQDSmzFg5M8+8BUTL0blrBa1evdNZ5fCkI0kAnvSlD6K6+ycAfQdAbXhwBGLpDTD/HFXKtdTa+mYgFjwojRQBuL6+BlXVM8E8G0TDPcQVxapvgfkyFLQbCdCj4mBkCMDp9Fmg1M0AxkYFnGD84CcBvojy+T8Ho9+Z1rITgLPZw9CDa0GY4sz1GEvLyIHwa1SlZpZ7kqmsBOB0VgX47tLs3FAszK9BxznUpj1YrvDLQgCuq6tC7ejlIFxSrsAjZLcHuj4PxfwSAjhsv0InAGcyB0OnlSCcGHawkbbHWIOerrOpvX17mH6GSgBOp8cDyv0gel+YQcbI1mZA/wLl8y+H5XNoBOCGzGehoN23BZqwEArbjvQLoJ9JhcITYZgOhQCczp4D4C4QhoURVOxtML8NHfVhdA4DJwA3Zi6GQjfGPinlCEDns6mo/T5I04ESgBszXwLhtyAK1E6QAJVVN3M3GGdRUVsblB+BJYZV9XNgWgOiqqCcHxJ6mXdCV06nttaNQcQbCAE4k/kEdDyWdPh8S9kb0LtPpGLxBd80mop8J4C5S+cxAAf67eyQ1sf8MrpSJ9Dqldv8xMFXAhgbN7r+XvkLOn6mwIEuxh9RyJ3i54yhvwRQs7cCuMBBSImoUwSYF1FBm+u02kDyvhGAVXUyoKz0y7FEz4AI6NBRR8XcQ35g5AsBOJ0+BKQ8A9AoP5xKdFggwPxvVCkf9WOHkT8EUDMPAPT5JHGhInAb5XMXerXomQClaV5Ci1dHkvouEOjhE6hNW++i5t4qngjAdXXDUbv/iyD6gBcnkrpuEeAnKa+Nd1tb6nkjgJpdBGCOFweSuh4RYL6YCtpNbrW4JgCr6hFgZVOywucWet/qvYldO8fSmjVvudHongDp7J0gfNWN0aSO7wgso3xulhutrgjADQ0fQmqYzEsrbowmdfxGgHdgR83BtK5FjrY7Ku4IoGZvAPBdR5YS4aARmE353FKnRhwTgFV1DKBsAVDt1FgiHygC27D1X4fShg17nFhxToB0Zj6IFjgxksiGhADzt6mg3eLEmnMCqJktAB3sxEgiGxICjEepkDvNiTVHBODG7KlQ8IgTA4lsyAh0dx1G7e3/smvVGQHSmRtBdLFd5YHKHXgg8N//AnpkDtoGGq5t5cxzqaDJBJ2tYpsApeNco0a/BuAAW5qDFBo/HlgwD3jxJeCyucDbbwdpbWDdigLM+Clw6qnAL24C2u8rjx/vssrPU147yq4j9gmgqllAabWrOFC5SWcB3zNHoc9vBi6dA+zeHajJ/1OeSgFzZgMTJhg/rWwFbr0tXB8GsqbTBCq2brDjjAMCZH4J0LfsKA1cRp68WTOBk08yTG3aBMyZC3R1BW66ZKBv8p97zrAfNgkHipa5iQqarZGaEwI8B9C4cBC2YaWUhEuBCZ82hDc+AVy+EOjutlHZg0jUky+hMdZRIWdrf4YtApRu3GSK3vVnVVXAvLnAJ80V0fUbgMXNQE+PhwwPUrVv8p95Fpi/IDpPfq/rzLsxoqaWWlosgbBHgMbMuVDo7mBQ9ai1uhpomg8ce6yh6PE/AkuX+T866C/5c+eF99pxChPx6aRplkN2ewSI0vCvPyBqaoAlzcC4Dxu/PvQw8LPlgNzh6EeJW/IlZl2fQ8V8s1X4NgmQfRwEs8dlpbJMv++3H7C0GRhr3jG1dh1wzbXeSRDH5Bsp+B3lc3Iqe9BijwBqdkcs7usbVQssXQocdqgR9KrVwA0eDibHN/nSE7S1XcySADxlyoHo1l+3YlJkfh89Gli2BDjYXK5wOz6PdfIl/9xJBc3yrkVrAqTTp4BSj0YmwXYcec97gCuXAe9/vyF9z73AXQ76sHFPfi9GNtYFbBAgez4It9vBPVIyY8YAVywF5F8pMksnrYFVkeTPl6GleRG5DPWi3NsfNB79DMrnHxhMxJoAalb2mi2xwi2Svx90kEECaRGkrLgFKBQHdrVv8p/6G9B0eXSHelagM3+dCtqd3giQzjSDaLaVrcj+Ln0B6RNI30CKdAqlc9i39Jf8BU3AHkcbbKIFg87fpaL2C68EuB5E34tWZA69kVGBjA5klCBzAzJHIHMFvaUSk2/ENovyuWXeCKBm7wDwDYeQR09c5gdknkDmC2QPgcwWyqxh5SZfctBM+dygB3es+wDprNzqOTl6GXXhkcwUyoyhzBzKesGyK4CG+n0dPnnnx73ZfycszNdTQZvmrQVIZ+8P+YNMLjLroIqsGcjagawhvLNUWvJLsfHtlNcGvbDDugVQM+0AfdEBxNEXlSVkWUXsLZs3AzNmxbvD1x/qzL+mgjboHg5rAqSzLSBYzilHP+umh8OGAXPn7Gv25c+ykWR+E/D007EJw5ajjOuokPuBt1dAJd37I8lfMB847uMGJs9uMlYQZV9BZydw2Txjd1HllMWUz13mjQDpTPyHgYJA3+Rv3Ag0LQRkg6m0CDIakC1d8ip46aVKoYDlcTHrV4CalfNmM2ONiHT45s/b9+SvXw8sXrJv59BJJwKzZwGy13BHBzBrFvCK7a310YWGeRoVtOu9tQCN6g+hKFdHN0oLzyT5Cy8HPvoRQ7Bv8nurn3YqMGM6INcav/UW8NMZwKuvxjZsw3H9HMrnf+eRAJlGKFSIJRLDhwNNC6yT3xtc3eeAH/3QIMHrrxuvA/k3roV4PGnak94I0DDlaKT0+PWMJPmLFwJHmWckBnry+6Jz5kRg2veNv27bBkyfaZxAimMZXl1DLS2D7pW37gPIJRBqVpSkYoNB3+Q/9jhwxZX2dwvX1wMXy0dLAWzdCkyfYfQN4lSYt1BBM7dGDey4JQGkKquZaJ0JGCwRMte/sGnfk+80+b26MypwkTmHIqOCWZeW7wiaG+LZPBtglwBtANW78SPUOpL85sXAkUcYZiX5Mt/v9gDpV88Dzv2KoatcR9BcA8grKK+ZzZjXFiCdWQiiQScUXPvpV0W/k9/rl7QC0hpICfsImhdsdP4OFbUVVirstQDpyXUgDuyzJVZOWv5eWwssWrjvyX/wIeCqq90/+X0NSn9A+gVSwjqCZhm0hQDxUaRpz1upsUeAqVNT2NXZEckvekvyZZ3/8MONWP1Ofi+Cl0wDJp5h/E+OoC1a7B/BrLLk/PdtlM8dZKeaLQKIIk5n14BgImBHdQgy/SV/+VXeD4P057rMDcgcgcwVSPH79JG/cNm+SNo+ARrVS6Eoi/3104O2/fcHmhe9+8kPKvm9bgoJZLZQZg2l+HX6yAMM/Vel8ynf+hs7au0ToD77MVQhGuul8uTLvv9DDjFifGAtcO11wTz5fVGU9QJZN5D1AyleTx/ZyZIzmR6kaIzdbwnYJoD5GngKBHMt1ZlXvkpPmQxcaG50keTLGcAwi5BANpQc/ynDqmwd3/CXMD0YxBa3U15rsOuMMwJE5TVw5JHAzOnGev7V19iN1V852UMg19Scfhrwk+nAy6F979kiDvvNvyhyRoD6+kNRVf2Kv0gm2nxDQC6G6No9hlav3mlXpyMClF4DauYxgE62ayCRCxEBxm+pkJvqxKJzAjRmvgmFfuXESCIbEgI9LF8cd3RXnXMCyKTQ7s6Xk+tiQ0qqbTP8NOU1xx10xwQwRgOZH4NouW3fEsHgEdD5PCpq9zg15I4AxseitoLIPHbr1Gwi7ysCjH+ikDvCzSdlXRHAbAUWgGi+r4Ekytwh4OKa+F5D7glQN7UWtZ0vgOh97rxOavmCAPML6Nh+DK1b5+qGTNcEMIaE6gWAIh+MTkrZELC+BWQw1zwRwHgVxOAKubIlJ2jD/HvKa2d7seIDAdLjAeWvIFkqS0poCDB3omfPOCcfh+jPN1+SxmrmZoC+HVrwiSFA53lU1BZ6hcIfAkiHcFTXUwDMazq9upXUHxQB5ifQsX2C247fO3X7QgCjL5AeD0r9KfmcXNDk5R3o6T6O2tr+6Ycl3whgkCDzAxCVaX3WDzhioEPnLBU1zS9PfSWAOSoogmB7Q4JfgQwJPcw3UEEzz635E7H/BCj1BzofBMjcLuOPo0NeC2MdRlRPtPMRCCdY+U6AUiswadJ7UT1c+gPmBf5OXEpk/w8B6fSNqDmFWlp2+Y1OIAQokSCbPQw6/xkgW/vT/Q6sYvTJVO+ezhNo1ar/BBFTYAQokUBVjwVIdhCNCsL5ytfJW9G95ySvkz2D4RQoAUokaMiehBRWATAv6638tPkTIb8KcB3l88/6o69/LYEToESCTOYY6LQWhA8GGUzl6OZNYP0MKhS2BB1TKAQokUB2FKeG3Q8i87KeoEOLq35ej+49Z1B7+/YwIgiNACUSTJw4GsNHrgbBPFYTRogxssFYg56uNLW3d4bldagEMFuCGqSGXR2Zr5CHhfTgdnQwN2NEzQK/x/lW4YVOgF6H2PgYtXyKZoh3DvlVEM6285FHq2S6+b1sBNjXL6huBcH8ALCbEOJch9eiKjWVVq58o1xRlJUAJRKUzhl0XQJw05CZL2B+DaCZKORuc7OT10+ylJ0Ae18JDQ0fgFIlfYNz/QwwYrrkXb8CVcpsu8e3g/Y/MgTYS4TG7Geg8C0AHRN08KHqZ/4LFFxEmvbXUO1aGIscAfa+FnZ1SkswHUTmt+GjBJsTX/gRgK9EPq+Vu7nvz+tIEuCdjnImcyZ0mhGzz9boYGjQeTG1aeud0CVs2cgTYF8fIXscUjgfzOeByLwbJmy4LOzJyh1wJxTcYeeKtih4HxsCvKtVUNWTAfoKGFMjQIbNYL0FeupeamvdGIWkOvEhlgTY2yrIDSeNk4+Hwl+U1Qbz4oqgL7XuAvhhgNqhd99HxeLfnAAeNdlYE6AvmOZag5DhRBCOBmOcx8WnXWBslpuCAf4HiB9FZ+caJ1ewRC3hff2pKAIMBDar6uHoobFQaDSYR0KhWuh6LRRlJHSdQUoHSN8JVjrA3IEU3gTwImna1qgn0Kt/Q4IAXkGq5PoJASo5uzZiSwhgA6RKFkkIUMnZtRFbQgAbIFWySEKASs6ujdgSAtgAqZJFEgJUcnZtxPY/UiMozE1r8CMAAAAASUVORK5CYII=);
        }

        .title {
            font-size: 24px;
            color: #333333;
            text-align: center;
            margin-bottom: 20px;
        }

        .remarks {
            text-align: center;
            color: #909399;
            font-size: 14px;
            line-height: 14px;
            margin-bottom: 25px;
        }

        .list {
            padding: 30px;
            margin: 15px;
            background-color: #F4F4F5;
            border-radius: 5px;
            margin-bottom: 50px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .item {
            color: #666666;
            margin-bottom: 10px;
        }

        .item::before {
            content: '';
            width: 20px;
            height: 20px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 5px;
            margin-top: -5px;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAEj0lEQVRYR+2XS2wVdRTGf2dub6Gg0ZqIiELwQURDqUFWIhK1ttzeeVREFNGFjwWaGBbGmEA06sKoYcVC4kLSGHEDNZ1H79WGFIIQgiLhYWI0agKFgAoBNLzS3jlmpu319r4LC2LiLGfO/5zvfP/vPEa4xo9c4/j8twCobc8nxEZoA25DuTVmUDiByjFUB0jgiucdqpfZuhjQTnshCTaCLKzLsbKXkDWScffWsq8KQFOpSSSSHyPy4qijEGU7qt2QO8Dly4Oybdu56Jva9gxyrER4GZG5sb3qJnJDr0o2e7kSkIoA1LJmgREALagqwlaEteJ5v1TLSsHA6loF4QcgM4DDEJri+0fLnSsLQC1rCsh+kHtQPYWyQvq87bXoLPyuqdTtNCS3xT7Qn0AXiO9fKPZRAYCzFXgS1T/JDS2QbPbYRIKP2Wo63YzR8B1wF9Ajvru8JgDttJeSkCzKEOQekiD49kqC50FY1lww9gNNhCyVPvfrQn8lDKjpHEaYh+pHEnhvXk3wAhCvgbEh0oP47vyKADRtP4IhA6j+xaULs8YUHgs6ba1FjOcIh1KSyRwpB0zTXQ8g4Zco70qftykPIOoUpnMoToxwsfj+rrFv4xhQ096IyGqUDRK4a8aJyrKWgNEf6yIcXlQMQtPpezEa9qCaJGSJZLx9RedHWChidjwAy/4N5A4096AEwZ7iLNV0OkFd4PdCEGqac8DYDTSD8bgEvTtKznZ2TieRPFF8DcUMDCHSwN/nkrJjx3BZmi2rC4weVE9EIEgkDNTYjXALhCvE93sq6UYt5xSqkyXwriu5Au3ouInGyadRjkjgzq7abKyu50E/Q/U4IkPAbAhfEN/vrnrOtA8g0kpCmqW392xkm2dAu7puJKdn6gEwIkr7LQx5bzTg++K762pVjFr2QZD5ZQHETk37PEhSArexaiap1M0kkjsRuRPIxcIkjHRzvAYDf0Q6kcBLlq8Cy4nGaAuicyr1/PiqkpMiwd1NiIXIeUSzwOlqILSt7QYmTzmD8Kv43pwKAOz1IK8Db4jvri9R8ohOIoXfR6jLpM/zRq4jrv8B4Czh8MPl+oSazrMIm4FPxHdXlweQdhZjsBPlRwJ3nkCYbyZRBk1Tv4mDK89I4EbzIv/Ey4rGTexS2T5h2T0gywj10cLBVtqKx65BWSWB+8W/7dR5G9V3QFYUB8/bRP1AErtQ9krg2vn3tr0Ilej9DxK4LYXASwGknQ4MvkL1KA1G61i5qG1fj2pzpbk+nqmmpPj+qfh6otGu8n28pGiuQ4KgvyqAkWpwtiAsR+kncJcKaK0Sq9x8RqlXtkrgPlVsV3shQbNcvLCycDDVA0bb26fS2NQ9msjPNDXeL1u2XKwLQMyC48wkpC8uSxgEWYff+3k9bKjpLAf9cLRPHMYgLa47WA74xJbSqDpUP0WHN0smc3JcFbQ/MY1Jw0+j8lLcbuMsrmIpHee83Foe7YrCICohwkxgWkFR7iPHK8UjecIMlDQi02xFDRuDx+IfE2T6iI2ezP+YSOhJEBysRyeRTV0/JvU6uxK7/wH8Ax7BCD8rPXU0AAAAAElFTkSuQmCC);
        }
    </style>
</head>

<body>
    <div class="main">
        <div class="icon-box"></div>
        <div class="title"><?php echo $t; ?></div>
        <div class="remarks"><?php echo $r; ?></div>
        <?php
        $l = '';
        if (isset($i)) {
            foreach ($i as $k => $v) $l .= '<div class="item">' . $v . '</div>';
        }
        if ($l) $l = '<div class="list">' . $l . '</div>';
        echo $l;
        ?>
    </div>
    <script>
        if (window.top != window.self) {
            parent.location.reload();
        }
        setTimeout(function() {
            parent.location.reload();
        }, 10000);
    </script>
</body>

</html>