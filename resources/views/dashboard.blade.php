@extends('layouts.app')

@section('content')
    <div class="container">
        <table class="table">
            <tbody>
                <tr>
                    <td class="text-right">Дата создания:</td>
                    <td><?= $created_at ?></td>
                </tr>
                <tr>
                    <td class="text-right">Оригинальная ссылка:</td>
                    <td>
                        <a href="<?= 'https://' . $url ?>"><?= $url ?></a>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Короткая ссылка:</td>
                    <td>
                        <a href="<?= '/' . $short_url ?>"> <?= 'shortoner.test/' . $short_url ?> </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <statistics-component id-link="<?= $id ?>"></statistics-component>
@endsection