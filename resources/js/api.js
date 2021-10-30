import axios from 'axios';

const URL_GAMES = '/games',
  URL_MATCH = '/api/match/',
  URL_ROUND = '/game-rounds',
  URL_MOVE = '/game-histories',
  URL_UPDATE = '/games/'

export default {
  games: () => {
    return axios.get(URL_GAMES);
  },
  game: ({id}) => {
    return axios.get(URL_MATCH + id);
  },
  newround: (form) => {
    return axios.post(URL_ROUND, form);
  },
  move: (form) => {
    return axios.post(URL_MOVE, form);
  },
  create: (form) => {
    return axios.post(URL_GAMES, form);
  },
  update: (id, form) => {
    return axios.put(URL_UPDATE + id, form);
  },
  destroy: ({id}) => {
    return axios.delete(URL_UPDATE + id);
  },
};
