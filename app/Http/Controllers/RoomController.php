<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Generar dinámicamente los asientos con un máximo de 30 asientos
     *
     * @return array
     */
    private function generateSeats()
    {
        $seats = [];
        $rowLetters = range('A', 'Z'); // Generar letras desde A hasta Z

        // Establecer el máximo de 6 filas y 5 columnas para obtener 30 asientos
        $maxRows = 6; // Máximo de 6 filas
        $maxColumns = 5; // Máximo de 5 columnas

        // Generar los asientos
        foreach (array_slice($rowLetters, 0, $maxRows) as $row) {
            for ($col = 1; $col <= $maxColumns; $col++) {
                $seatNumber = $row . str_pad($col, 2, '0', STR_PAD_LEFT);
                $seats[$seatNumber] = false; // Inicialmente todos los asientos están desocupados
            }
        }

        return $seats;
    }

    /**
     * Crear una nueva sala con 30 asientos generados dinámicamente
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        // Generar los asientos para la sala (siempre 30 asientos)
        $seats = $this->generateSeats();

        // Crear la sala
        $room = Room::create([
            'name' => $validated['name'],
            'total_seats' => count($seats),
            'seats' => $seats,
        ]);

        return response()->json(['message' => 'Sala creada con éxito', 'room' => $room], 201);
    }

    /**
     * Actualizar el estado de los asientos de una sala
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Room $room
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSeats(Request $request, Room $room)
    {
        $validated = $request->validate([
            'seats' => 'required|array', // Lista de asientos a actualizar
            'seats.*' => 'string|regex:/^[A-Z][0-9]{2}$/', // Validar el formato del asiento (ej: A01)
        ]);

        // Obtener los asientos actuales de la sala
        $seats = $room->seats;

        // Marcar como ocupados los asientos especificados
        foreach ($validated['seats'] as $seat) {
            if (isset($seats[$seat])) {
                $seats[$seat] = true; // Marcar el asiento como ocupado
            }
        }

        // Actualizar los asientos en la sala
        $room->seats = $seats;
        $room->save();

        return response()->json(['message' => 'Asientos actualizados con éxito', 'room' => $room], 200);
    }

    /**
     * Obtener la lista de salas
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $rooms = Room::all();
        return response()->json($rooms);
    }

    /**
     * Mostrar los detalles de una sala específica
     *
     * @param \App\Models\Room $room
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Room $room)
    {
        return response()->json($room);
    }

    /**
     * Eliminar una sala
     *
     * @param \App\Models\Room $room
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json(['message' => 'Sala eliminada con éxito'], 200);
    }
}
