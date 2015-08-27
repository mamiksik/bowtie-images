# BowTieImages

## Installation
1) Use composer to get all needed code [Composer](http://getcomposer.org/):
```sh
$ composer require mamiksik/BowtieImages
```

2) Register as Configurator's extension:
```
extensions:
	bowtieImages: BowtieImages\ImageExtension
```

3) Enjoy Just-in-time generated image!

##About
BowtieImages(shor BTI) is JIT generator for diferent size of images. BIT can generate any size of image you want.

##Cache
<<<<<<< HEAD
Veškeré vygenerované obrázky jsou uloženy. Tím, že se obrázky uloží do cache zvýšíme razantně rychlost načítání.
Skvěle je zde vyřešena i invalidace nepoužívaných obrázků.

###Životní cyklus
1. Request pro požadovanou velikost. 
2. Provider se pokusí nalézt odpovídající obrázek v cache.
	1. Obrázek byl nalezen v cache, odešle se.
	2. Obrázek nebyl nalezen v cache, vyhledá se originál, vygeneruje se požadovaná velikost, uloží se do cache a obrázek se odešle.

##Použití
=======
Using nette cache we can provide fast load of image that are cached and we can save space on your disk. If cached image sizes are more than 7 day inload the cache automatically throw this images away!

##Usage
>>>>>>> parent of 3ee87c1... readme cz
```
example.cz/images/WidthxHeight/flag/nameOfimage.type
```

<<<<<<< HEAD
1. url prefix "images" je konfigurovatelný (více sekce config) 
2. šířka[width] a výška[height] jsou volitelné (lze uvést i pouze šířku)
3. flag je volitený, implementovány jsou tyto: `fit`(výchozí), `fill`, `exact`(vyžaduje šířku i výšku) a `stretch`(vyžaduje šířku i výšku) (v pozadí se používá nette [Image](https://doc.nette.org/cs/images#toc-zmena-velikosti))
=======
url prefix "images" is configurable(see more in config section)
width and height is optional, also you can type only width
flag is optional supporting this flag: fit, fill, exact and stretch(in behavior this using nette [Image](https://doc.nette.org/cs/images#toc-zmena-velikosti))
>>>>>>> parent of 3ee87c1... readme cz

###How route looks like
```
/<namespace>[/<width>x[<height>]][/<flag>]/<filename><type .png|.gif|.jpg|.jpeg>
```


##Config
<<<<<<< HEAD
BTI lze konfigurovat, ale není to nuté. Vše funguje "out of the box".
=======
If you want you can config some thing, but you didn't have to because BIT works out of the box.
>>>>>>> parent of 3ee87c1... readme cz

```
bowtieImages:
	wwwDir: %wwwDir%
	urlPrefix: images #prefix in url
	dataPrefix: data #prefix in wwwDir can not be same as urlPrefix
	cacheInvalidationTime: 7 days #nette cache expire time
```
<<<<<<< HEAD
 Cache používá `Cache:SLIDING`, takže pokaždém načtení konkrétního obrázku je expirace pro konkrétní obrázek resetována.
=======
 Cache using `Cache:SLIDING` so every time image is load expire time is reset.
>>>>>>> parent of 3ee87c1... readme cz

