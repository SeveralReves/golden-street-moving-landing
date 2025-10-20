// Carga única (v2 funcional)
import { setOptions, importLibrary } from '@googlemaps/js-api-loader';

let booted = false;

export function initGoogle(apiKey) {
  if (!booted) {
    setOptions({ key: apiKey, v: 'weekly' }); // 👈 solo 1 vez
    booted = true;
  }
}

let placesPromise = null;
export function loadPlaces() {
  if (!placesPromise) placesPromise = importLibrary('places');
  return placesPromise;
}

let mapsPromise = null;
export function loadMaps() {
  if (!mapsPromise) mapsPromise = importLibrary('maps'); // bounds/circle opcional
  return mapsPromise;
}
