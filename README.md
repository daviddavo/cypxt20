# Backend de Cuentos y Poemas por Teléfono

Esto es el monolítico formulario de peticiones y panel de control de cuentos y
poemas por teléfono.

### ¿Qué es cuentos y poemas poemas por teléfono?
El día del libro, si llamas por teléfono a la Biblioteca Municipal de Mejorada del Campo podrás escuchar cuentos. Es una iniciativa del grupo de chicos y chicas [Montando el Local](https://montandoellocal.com/acerca-de/) en colaboración con la biblioteca para promover la lectura.

## Instalación

> **DISCLAIMER**: Aunque en Montando el Local usemos esta página en entorno de producción,
> no es nada recomendable. Este proyecto comenzó por un chaval de 15 años sin experiencia
> en programación, y no se ha realizado ningún esfuerzo en mejorar su estabilidad.

Antes de seguir, necesitaras instalar `php`, `yarn` y `symfony`

1. Primero, nos metemos con el tema dependencias basadas en PHP con composer
```
composer install
```

2. Ahora instalamos las dependecias de yarn
```
yarn install
```

3. Luego, ejecutamos los scripts de compilación de yarn
```
yarn encore prod
```

#### La Base de Datos

Para crear la base de datos sólo necesitarás usar el siguiente comando:

```
bin/console doctrine:database:create
```

Y luego crear las distintas tablas dentro de la base de datos con

```
bin/console doctrine:migrations:migrate
```

Ya sólo te queda meter las líneas a las tablas. De momento esto sólo es posible
conectándose a la BBDD y ejecutando los SQL (o usando algún tipo de IDE).