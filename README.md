# BowTieImages

## Installation
1) Na instalaci použíjte [Composer](http://getcomposer.org/):
```sh
$ composer require mamiksik/bowtie-images @dev
```

2) Registruje extension v config.neon:
```
extensions:
	bowtieImages: BowtieImages\ImageExtension
```

3) Užíjte si Just-in-time generování obrázků!

##About
BowtieImages(BTI) je JIT generátor pro různé velikosti obrázku.

##Cache
Veškeré vygenerované obrázky jsou uloženy. Tím, že se obrázky uloží do cache zvýšíme razantně rychlost načítání.
Skvěle je zde vyřešena i invalidace nepoužívaných obrázků.

###Životní cyklus
1. Request pro požadovanou velikost. 
2. Provider se pokusí nalézt odpovídající obrázek v cache.
	1. Obrázek byl nalezen v cache, odešle se.
	2. Obrázek nebyl nalezen v cache, vyhledá se originál, vygeneruje se požadovaná velikost, uloží se do cache a obrázek se odešle.

##Použití
```
example.cz/images/WidthxHeight/flag/nameOfimage.type
```

1. url prefix "images" je konfigurovatelný (více sekce config) 
2. šířka[width] a výška[height] jsou volitelné (lze uvést i pouze šířku)
3. flag je volitený, implementovány jsou tyto: `fit`(výchozí), `fill`, `exact`(vyžaduje šířku i výšku) a `stretch`(vyžaduje šířku i výšku) (v pozadí se používá nette [Image](https://doc.nette.org/cs/images#toc-zmena-velikosti))

###Routa v "nette/route" zápisu
```
/<namespace>[/<width>x[<height>]][/<flag>]/<filename><type .png|.gif|.jpg|.jpeg>
```


##Config
BTI lze konfigurovat, ale není to nuté. Vše funguje "out of the box".

```
bowtieImages:
	wwwDir: %wwwDir%
	urlPrefix: images #prefix v url
	dataPrefix: data #prefix pro www složku. Nesmí být stejné jako urlPrefix!
	cacheInvalidationTime: 7 days #nette cache expire time
```
 Cache používá `Cache:SLIDING`, takže pokaždém načtení konkrétního obrázku je expirace pro konkrétní obrázek resetována a navíc `Cache:FILES` což zapřičiní že po smazání originálu se smažou i ostatní verze obrázku.

##Co je v plánu
Testy, testy a texy! :)

Pravidla pro velikosti generovaných obrázků

Rozhraní pro ukládání, obměnu, či mazání originálních položek

Podpora pro latte, s možnosti volby `LQIP`