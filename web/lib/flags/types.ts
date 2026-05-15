export type FlagDecisions = Record<string, boolean>;

export type EvaluateRequest = {
  user_id: string;
  attributes?: Record<string, string>;
};

export type EvaluateResponse = {
  flags: FlagDecisions;
};
