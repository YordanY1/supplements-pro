import axios from "axios";
window.axios = axios;

import { loadStripe } from "@stripe/stripe-js";

window.loadStripe = loadStripe;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
